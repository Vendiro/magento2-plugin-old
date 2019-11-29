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

use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Service\Order\Import;
use TIG\Vendiro\Test\TestCase;

class ImportTest extends TestCase
{
    protected $instanceClass = Import::class;

    public function testSaveOrder()
    {
        $newOrderId = rand(1, 100);
        $orderTestData = [
            'id' => rand(1, 100),
            'marketplace_order_id' => 'marketplace-' . rand(1, 100),
            'marketplace' => [
                'name' => 'TIG Shop',
                'reference' => 'tig123'
            ]
        ];

        $orderMock = $this->getFakeMock(OrderInterface::class)
            ->setMethods([
                'setOrderId', 'setVendiroId', 'setMarketplaceOrderid',
                'setMarketplaceName', 'setMarketplaceReference'
            ])
            ->getMockForAbstractClass();
        $orderMock->expects($this->once())->method('setOrderId')->with($newOrderId);
        $orderMock->expects($this->once())->method('setVendiroId')->with($orderTestData['id']);
        $orderMock->expects($this->once())->method('setMarketplaceOrderid')->with($orderTestData['marketplace_order_id']);
        $orderMock->expects($this->once())->method('setMarketplaceName')->with($orderTestData['marketplace']['name']);
        $orderMock->expects($this->once())->method('setMarketplaceReference')->with($orderTestData['marketplace']['reference']);

        $orderRepoMock = $this->getFakeMock(OrderRepositoryInterface::class)
            ->setMethods(['create', 'save'])
            ->getMockForAbstractClass();
        $orderRepoMock->expects($this->once())->method('create')->willReturn($orderMock);
        $orderRepoMock->expects($this->once())->method('save')->with($orderMock);


        $instance = $this->getInstance(['orderRepository' => $orderRepoMock]);
        $instance->saveOrder($newOrderId, $orderTestData);
    }
}
