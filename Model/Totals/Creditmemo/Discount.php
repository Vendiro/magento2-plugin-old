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
namespace TIG\Vendiro\Model\Totals\Creditmemo;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use TIG\Vendiro\Api\OrderRepositoryInterface;

class Discount extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param PriceCurrencyInterface   $priceCurrency
     * @param OrderRepositoryInterface $orderRepository
     * @param array                    $data
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($data);

        $this->priceCurrency = $priceCurrency;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return $this|AbstractTotal
     */
    public function collect(Creditmemo $creditmemo)
    {
        if (!$this->validVendiroOrder($creditmemo)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($creditmemo);
        $discount = $this->priceCurrency->convert($baseDiscount);
        $creditmemo->setDiscountAmount($creditmemo->getDiscountAmount() + $discount);
        $creditmemo->setBaseDiscountAmount($creditmemo->getBaseDiscountAmount() + $baseDiscount);

        $grandTotal = $creditmemo->getGrandTotal() + $discount;
        $baseGrandTotal = $creditmemo->getBaseGrandTotal() + $baseDiscount;
        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        return $this;
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return bool
     */
    private function validVendiroOrder($creditmemo)
    {
        $order = $creditmemo->getOrder();
        $foundVendiroOrders = $this->orderRepository->getByFieldWithValue('order_id', $order->getIncrementId());

        //Validate if order came from Vendiro
        if (empty($foundVendiroOrders) || strpos($order->getDiscountDescription(), "Vendiro") === false) {
            return false;
        }

        $baseDiscount = $order->getBaseDiscountAmount();

        // there's no need to keep going if no discount exists
        if ($baseDiscount === null || $baseDiscount == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return float|int
     */
    private function getBaseDiscount($creditmemo)
    {
        $order = $creditmemo->getOrder();
        $baseDiscount = $order->getBaseDiscountAmount();

        // Make sure the discount is consistently a negative number to avoid incorrect calculations
        if ($baseDiscount > 0) {
            $baseDiscount = $baseDiscount * -1;
        }

        return $baseDiscount;
    }
}
