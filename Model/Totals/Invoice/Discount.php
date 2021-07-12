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
namespace TIG\Vendiro\Model\Totals\Invoice;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Discount extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param array                  $data
     */
    public function __construct(PriceCurrencyInterface $priceCurrency, array $data = [])
    {
        parent::__construct($data);

        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this|AbstractTotal
     */
    public function collect(Invoice $invoice)
    {
        // there's no need to keep going if no discount has been set
        if ($this->hasNoVendiroDiscount($invoice)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($invoice);

        $this->applyDiscount($baseDiscount, $invoice);

        return $this;
    }

    /**
     * @param Invoice $invoice
     *
     * @return bool
     */
    private function hasNoVendiroDiscount($invoice)
    {
        $baseDiscount = $invoice->getVendiroDiscount();

        return ($baseDiscount === null || $baseDiscount == 0);
    }

    /**
     * @param Invoice $invoice
     *
     * @return float|int
     */
    private function getBaseDiscount($invoice)
    {
        $baseDiscount = $invoice->getVendiroDiscount();

        // Make sure the discount is consistently a negative number to avoid incorrect calculations
        if ($baseDiscount > 0) {
            $baseDiscount = $baseDiscount * -1;
        }

        return $baseDiscount;
    }

    /**
     * @param int|float $baseDiscount
     * @param Invoice   $invoice
     */
    private function applyDiscount($baseDiscount, $invoice)
    {
        $discount =  $this->priceCurrency->convert($baseDiscount);

        $invoice->setDiscountAmount($invoice->getDiscountAmount() + $discount);
        $invoice->setBaseDiscountAmount($invoice->getBaseDiscountAmount() + $baseDiscount);

        $grandTotal = $invoice->getGrandTotal() + $discount;
        $baseGrandTotal = $invoice->getBaseGrandTotal() + $baseDiscount;

        $invoice->setGrandTotal($grandTotal < 0.0001 ? 0 : $grandTotal);
        $invoice->setBaseGrandTotal($baseGrandTotal < 0.0001 ? 0 : $baseGrandTotal);
    }
}
