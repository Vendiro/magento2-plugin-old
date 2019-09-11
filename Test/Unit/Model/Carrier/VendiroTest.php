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
namespace TIG\Vendiro\Test\Unit\Model\Carrier;

use Magento\Framework\App\State;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use TIG\Vendiro\Model\Carrier\Vendiro;
use TIG\Vendiro\Test\TestCase;

class VendiroTest extends TestCase
{
    protected $instanceClass = Vendiro::class;

    public function testCollectRates()
    {
        $rateRequestMock = $this->getFakeMock(RateRequest::class)->setMethods(['getAllItems', 'getQuote'])->getMock();
        $rateRequestMock->method('getAllItems')->willReturn([$rateRequestMock]);
        $rateRequestMock->method('getQuote')->willReturnSelf();

        $appStateMock = $this->getFakeMock(State::class)->setMethods(['getAreaCode'])->getMock();
        $appStateMock->expects($this->once())->method('getAreaCode')->willReturn('crontab');

        $methodMock = $this->getFakeMock(Method::class)->setMethods(['setPrice'])->getMock();

        $methodFactoryMock = $this->getFakeMock(MethodFactory::class)->setMethods(['create'])->getMock();
        $methodFactoryMock->expects($this->once())->method('create')->willReturn($methodMock);

        $resultMock = $this->getFakeMock(Result::class)->setMethods(null)->getMock();

        $resultFactoryMock = $this->getFakeMock(ResultFactory::class)->setMethods(['create'])->getMock();
        $resultFactoryMock->expects($this->once())->method('create')->willReturn($resultMock);

        $instance = $this->getInstance([
            'appState' => $appStateMock,
            'methodFactory' => $methodFactoryMock,
            'resultFactory' => $resultFactoryMock
        ]);
        $result = $instance->collectRates($rateRequestMock);

        $this->assertEquals($resultMock, $result);
        $this->assertEquals($result->getAllRates()[0]->getData(), $methodMock->getData());
    }

    public function testCollectRatesReturnsFalse()
    {
        $rateRequestMock = $this->getFakeMock(RateRequest::class, true);

        $appStateMock = $this->getFakeMock(State::class)->setMethods(['getAreaCode'])->getMock();
        $appStateMock->expects($this->once())->method('getAreaCode')->willReturn('frontend');

        $instance = $this->getInstance(['appState' => $appStateMock]);
        $result = $instance->collectRates($rateRequestMock);

        $this->assertFalse($result);
    }
}
