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
namespace TIG\Vendiro\Test\Unit\Webservices\Endpoints;

use TIG\Vendiro\Webservices\Endpoints\GetAccount;
use TIG\Vendiro\Test\TestCase;
use TIG\Vendiro\Webservices\Rest;

class GetAccountTest extends TestCase
{
    protected $instanceClass = GetAccount::class;

    public function testCall()
    {
        $restMock = $this->getFakeMock(Rest::class)->setMethods(['getRequest'])->getMock();
        $restMock->expects($this->once())->method('getRequest')->willReturn('api call result');

        $instance = $this->getInstance(['restApi' => $restMock]);
        $result = $instance->call();

        $this->assertEquals('api call result', $result);
    }

    public function testGetEndpointUrl()
    {
        $instance = $this->getInstance();
        $result = $instance->getEndpointUrl();

        $this->assertEquals('account/', $result);
    }

    public function testGetMethod()
    {
        $instance = $this->getInstance();
        $result = $instance->getMethod();

        $this->assertEquals('GET', $result);
    }

    public function testGetRequestData()
    {
        $testData = ['data1' => 'value'];

        $instance = $this->getInstance();
        $instance->setRequestData($testData);

        $result = $instance->getRequestData();
        $this->assertEquals($testData, $result);
    }
}
