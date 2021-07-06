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
namespace TIG\Vendiro\Test\Unit\Service\Order;

use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use TIG\Vendiro\Service\Order\OrderStatusManager;
use TIG\Vendiro\Test\TestCase;

class OrderStatusManagerTest extends TestCase
{
    protected $instanceClass = OrderStatusManager::class;

    /**
     * @return array
     */
    public function createInvoiceProvider()
    {
        return [
            'can not invoice' => [false],
            'can invoice' => [true],
        ];
    }

    /**
     * @param $canInvoice
     *
     * @throws \Exception
     *
     * @dataProvider createInvoiceProvider
     */
    public function testCreateInvoice($canInvoice)
    {
        $orderId = rand(1, 100);
        $vendiroDiscount['discount'] = rand(1, 100);
        $invokeExpects = ($canInvoice ? 1 : 0);

        $invoiceMock = $this->getFakeMock(Invoice::class)
            ->setMethods(['setRequestedCaptureCase', 'register', 'getOrder', 'save'])
            ->getMock();
        $orderMock = $this->getFakeMock(Order::class)
            ->setMethods(['setVendiroDiscount', 'canInvoice', 'prepareInvoice', 'setIsInProcess'])
            ->getMock();

        $invoiceMock->method('setRequestedCaptureCase')->with(Invoice::CAPTURE_OFFLINE);
        $invoiceMock->expects($this->exactly($invokeExpects))->method('register');
        $invoiceMock->expects($this->exactly($invokeExpects))->method('getOrder')->willReturn($orderMock);
        $invoiceMock->expects($this->exactly($invokeExpects))->method('save');

        $orderMock->expects($this->once())->method('setVendiroDiscount')->willReturn($vendiroDiscount['discount']);
        $orderMock->expects($this->once())->method('canInvoice')->willReturn($canInvoice);
        $orderMock->expects($this->exactly($invokeExpects))->method('prepareInvoice')->willReturn($invoiceMock);
        $orderMock->expects($this->exactly($invokeExpects))->method('setIsInProcess')->with(true);

        $transactionMock = $this->getFakeMock(Transaction::class)->setMethods(['addObject', 'save'])->getMock();
        $transactionMock->expects($this->exactly($invokeExpects * 2))
            ->method('addObject')
            ->withConsecutive([$invoiceMock], [$orderMock])
            ->willReturnSelf();
        $transactionMock->expects($this->exactly($invokeExpects))->method('save');

        $orderRepositoryMock = $this->getFakeMock(OrderRepositoryInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();
        $orderRepositoryMock->expects($this->once())->method('get')->with($orderId)->willReturn($orderMock);

        $instance = $this->getInstance(['orderRepository' => $orderRepositoryMock, 'transaction' => $transactionMock]);
        $instance->createInvoice($orderId, $vendiroDiscount);
    }
}
