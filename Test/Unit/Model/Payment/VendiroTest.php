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
namespace TIG\Vendiro\Test\Unit\Model\Payment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\CartInterface;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Model\Payment\Vendiro;
use TIG\Vendiro\Test\TestCase;

class VendiroTest extends TestCase
{
    protected $instanceClass = Vendiro::class;

    /**
     * @return array
     */
    public function isAvailableProvider()
    {
        return [
            'extension enabled' => [
                true,
                true
            ],
            'extension disabled' => [
                false,
                false
            ]
        ];
    }

    /**
     * @param $enabled
     * @param $expected
     *
     * @dataProvider isAvailableProvider
     */
    public function testIsAvailable($enabled, $expected)
    {
        $cartMock = $this->getFakeMock(CartInterface::class)->getMockForAbstractClass();

        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)->setMethods(['getValue'])->getMockForAbstractClass();
        $scopeConfigMock->method('getValue')->with('payment/tig_vendiro/active')->willReturn(true);

        $vendiroConfigMock = $this->getFakeMock(Configuration::class)->setMethods(['isEnabled'])->getMock();
        $vendiroConfigMock->expects($this->once())->method('isEnabled')->willReturn($enabled);

        $instance = $this->getInstance([
            'scopeConfig' => $scopeConfigMock,
            'vendiroConfiguration' => $vendiroConfigMock
        ]);
        $result = $instance->isAvailable($cartMock);

        $this->assertEquals($expected, $result);
    }
}
