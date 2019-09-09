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

use Magento\Sales\Model\Order;
use TIG\Vendiro\Service\Order\Create;
use TIG\Vendiro\Service\Order\OrderStatusManager;
use TIG\Vendiro\Test\TestCase;

class CreateTest extends TestCase
{
    protected $instanceClass = Create::class;

    /**
     * @return array
     */
    public function updateOrderCommentAndStatusProvider()
    {
        return [
            'fulfilled by marketplace' => [
                23,
                [
                    'marketplace' => ['name' => 'TIG Marketplace'],
                    'marketplace_order_id' => 'TIG-058275',
                    'fulfilment_by_marketplace' => 'true'
                ],
                'Order via Vendiro<br>Marketplace: TIG Marketplace<br/>TIG Marketplace ID: TIG-058275<br/>Fulfilment by marketplace: true'
            ],
            'not fulfilled by marketplace' => [
                85,
                [
                    'marketplace' => ['name' => 'TIG Marketplace'],
                    'marketplace_order_id' => 'TIG-37974',
                    'fulfilment_by_marketplace' => 'false'
                ],
                'Order via Vendiro<br>Marketplace: TIG Marketplace<br/>TIG Marketplace ID: TIG-37974'
            ],
            'fulfilled by marketplace parameter does not exist' => [
                73,
                [
                    'marketplace' => ['name' => 'TIG Marketplace'],
                    'marketplace_order_id' => 'TIG-375378'
                ],
                'Order via Vendiro<br>Marketplace: TIG Marketplace<br/>TIG Marketplace ID: TIG-375378'
            ]
        ];
    }

    /**
     * @param $magentoOrderId
     * @param $vendiroOrderData
     * @param $expectedComment
     *
     * @throws \Exception
     *
     * @dataProvider updateOrderCommentAndStatusProvider
     */
    public function testUpdateOrderCommentAndStatus($magentoOrderId, $vendiroOrderData, $expectedComment)
    {
        $statusManagerMock = $this->getFakeMock(OrderStatusManager::class)
            ->setMethods(['addHistoryComment'])
            ->getMock();
        $statusManagerMock->expects($this->once())
            ->method('addHistoryComment')
            ->with($magentoOrderId, $expectedComment);

        $instance = $this->getInstance(['orderStatusManager' => $statusManagerMock]);
        $this->invokeArgs('updateOrderCommentAndStatus', [$magentoOrderId, $vendiroOrderData], $instance);
    }
}
