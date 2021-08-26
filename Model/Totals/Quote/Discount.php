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

class Discount extends AbstractTotal
{
    const VENDIRO_DISCOUNT_LABEL = 'Vendiro';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     *
     * Operations shouldn't be allowed in constructors, and thus the setCode() shouldn't be set here.
     * However, Magento's Total classes work in a way that this is necessary, even Magento itself does this.
     */
    //@codingStandardsIgnoreLine
    public function __construct(PriceCurrencyInterface $priceCurrency)
    {
        $this->setCode('vendirodiscount');

        $this->priceCurrency = $priceCurrency;
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
     *
     * @return bool
     */
    private function hasNoVendiroDiscount($quote)
    {
        $baseDiscount = $quote->getVendiroDiscount();

        return ($baseDiscount === null || $baseDiscount == 0);
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

        // A discount might already exist, which we don't want to discard
        if ($total->getDiscountDescription()) {
            $baseDiscount = $total->getDiscountAmount() + $baseDiscount;
        }

        return $baseDiscount;
    }

    /**
     * @param int|float $baseDiscount
     * @param Quote     $quote
     * @param Total     $total
     */
    private function applyDiscount($baseDiscount, $quote, $total)
    {
        $discount =  $this->priceCurrency->convert($baseDiscount);
        $items = $quote->getItems();

        foreach ($items as $item) {
            // @codingStandardsIgnoreStart
            if ($item->getPrice() < 0) {
                continue;
            }
            // @codingStandardsIgnoreEnd

            $item->setDiscountAmount(-$discount);
            $item->setBaseDiscountAmount(-$baseDiscount);
            $item->setOriginalDiscountAmount(-$discount);
            $item->setBaseOriginalDiscountAmount(-$baseDiscount);
        }

        $total->setDiscountAmount($discount);
        $total->setBaseDiscountAmount($baseDiscount);
        $total->setSubtotalWithDiscount($total->getSubtotal() + $discount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $baseDiscount);
        $total->addTotalAmount($this->getCode(), $discount);
        $total->addBaseTotalAmount($this->getCode(), $baseDiscount);
    }

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
        $amount = $total->getDiscountAmount();

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
