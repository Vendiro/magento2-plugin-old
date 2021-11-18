<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Vendiro\Model\Totals\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\SalesRule\Model\DeltaPriceRound;
use Magento\SalesRule\Model\Validator;

class Discount extends AbstractTotal
{
    const VENDIRO_DISCOUNT_LABEL = 'Vendiro';
    const DELTA_ROUND_TYPE = 'vendiro';
    const DELTA_ROUND_BASE_TYPE = 'vendiro_base';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var DeltaPriceRound
     */
    private $deltaPriceRound;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     *
     * Operations shouldn't be allowed in constructors, and thus the setCode() shouldn't be set here.
     * However, Magento's Total classes work in a way that this is necessary, even Magento itself does this.
     */
    //@codingStandardsIgnoreLine
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        DeltaPriceRound $deltaPriceRound
    )
    {
        $this->setCode('vendirodiscount');

        $this->priceCurrency = $priceCurrency;
        $this->deltaPriceRound = $deltaPriceRound;
    }

    /**
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total                       $total
     *
     * @return $this|AbstractTotal
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $quote->getAllVisibleItems();
        if (!$items || count($items) === 0) {
            return $this;
        }

        // there's no need to keep going if no discount has been set
        if ($this->hasNoVendiroDiscount($quote)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($quote, $total);
        $this->applyDiscount($baseDiscount, $quote, $total);

        $label = $total->getDiscountDescription() ? $total->getDiscountDescription() . ', ' : '';
        $total->setDiscountDescription($label . self::VENDIRO_DISCOUNT_LABEL);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return float|int
     */
    private function getBaseDiscount($quote, $total)
    {
        $baseDiscount = $quote->getVendiroDiscount();

        // Make sure the discount is consistently a negative number to avoid incorrect calculations
        if ($baseDiscount > 0) {
            $baseDiscount = $baseDiscount * -1;
        }

        return $baseDiscount + $total->getBaseDiscountAmount();
    }

    /**
     * @param Quote $quote
     *
     * @return bool
     */
    private function hasNoVendiroDiscount($quote)
    {
        $baseDiscount = $quote->getVendiroDiscount();

        return ($baseDiscount === null || $baseDiscount == 0);
    }

    /**
     * Return item base price
     *
     * This is a copy of the CartPriceRule calculation
     *
     * @see Validator::getItemBasePrice()
     * @param Quote\Item\AbstractItem $item
     * @return float
     */
    private function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price !== null ? $price : $item->getCalculationPrice();
    }

    /**
     * Return discount item qty
     *
     * This is a copy of the CartPriceRule calculation
     *
     * @see Validator::getItemQty
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return int
     */
    private function getItemQty($item)
    {
        return $item->getTotalQty();
    }

    /**
     * @param $items
     *
     * @return float|int
     */
    private function getTotalPrice($items) {
        $totalAmount = 0;
        foreach ($items as $item) {
            $itemPrice = $this->getItemPrice($item);
            $itemQty = $this->getItemQty($item);

            $totalAmount += $itemPrice * $itemQty;
        }
        return $totalAmount;
    }

    /**
     * @param int|float $baseDiscount
     * @param Quote     $quote
     * @param Total     $total
     */
    // @codingStandardsIgnoreStart
    private function applyDiscount($baseDiscount, $quote, $total)
    {
        $discount =  $this->priceCurrency->convert($baseDiscount);

        // Visible items are the items shown in cart, we apply our discount on those "total" rows
        $items = $quote->getAllVisibleItems();
        if (!$items || count($items) === 0) {
            return;
        }

        $totalPrice = $this->getTotalPrice($items);
        foreach ($items as $item) {
            $itemPrice = $this->getItemPrice($item);
            $itemQty = $this->getItemQty($item);

            $ratio = $itemPrice * $itemQty / $totalPrice;

            $item->setDiscountAmount(-$this->deltaPriceRound->round($discount * $ratio, self::DELTA_ROUND_TYPE));
            $item->setBaseDiscountAmount(-$this->deltaPriceRound->round($baseDiscount * $ratio, self::DELTA_ROUND_BASE_TYPE));
        }


        $total->setDiscountAmount($discount);
        $total->setBaseDiscountAmount($baseDiscount);

        $total->setSubtotalWithDiscount($total->getSubtotal() + $discount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $baseDiscount);

        $total->addTotalAmount($this->getCode(), $discount);
        $total->addBaseTotalAmount($this->getCode(), $baseDiscount);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     */
    //@codingStandardsIgnoreLine
    public function fetch(Quote $quote, Total $total)
    {
        $result = null;
        $amount = $total->getTotalAmount($this->getCode());

        if ($amount != 0) {
            $description = $total->getDiscountDescription();

            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }

        return $result;
    }
}
