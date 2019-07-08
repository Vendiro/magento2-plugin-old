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
namespace TIG\Vendiro\Test\Unit\Webservices;

use Magento\Framework\HTTP\ZendClient;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Api\AuthCredential;
use TIG\Vendiro\Service\Software\Data as SoftwareData;
use TIG\Vendiro\Test\TestCase;
use TIG\Vendiro\Webservices\Rest;

class RestTest extends TestCase
{
    protected $instanceClass = Rest::class;

    public function testSetUri()
    {
        $apiConfigMock = $this->getFakeMock(ApiConfiguration::class)->setMethods(['getModusApiBaseUrl'])->getMock();
        $apiConfigMock->expects($this->once())->method('getModusApiBaseUrl')->willReturn('http://google.com/');

        $zendClientMock = $this->getFakeMock(ZendClient::class)->setMethods(['setUri'])->getMock();
        $zendClientMock->expects($this->once())->method('setUri')->with('http://google.com/search/');

        $instance = $this->getInstance([
            'apiConfiguration' => $apiConfigMock,
            'zendClient' => $zendClientMock
        ]);

        $this->invokeArgs('setUri', ['search/'], $instance);
    }

    public function testSetHeaders()
    {
        $expectedHeaders = [
            'Authorization' => 'Basic abc==',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8',
            'User-Agent' => 'VendiroMagento2Plugin/1.2.3'
        ];

        $authCredentialMock = $this->getFakeMock(AuthCredential::class)->setMethods(['get'])->getMock();
        $authCredentialMock->expects($this->once())->method('get')->willReturn('abc==');

        $zendClientMock = $this->getFakeMock(ZendClient::class)->setMethods(['setHeaders'])->getMock();
        $zendClientMock->expects($this->once())->method('setHeaders')->with($expectedHeaders);

        $softwareDataMock = $this->getFakeMock(SoftwareData::class)->setMethods(['getModuleVersion'])->getMock();
        $softwareDataMock->expects($this->once())->method('getModuleVersion')->willReturn('1.2.3');

        $instance = $this->getInstance([
            'authCredential' => $authCredentialMock,
            'zendClient' => $zendClientMock,
            'softwareData' => $softwareDataMock
        ]);

        $this->invoke('setHeaders', $instance);
    }

    /**
     * @return array
     */
    public function formatResponseProvider()
    {
        return [
            'empty string' => [
                '',
                ['']
            ],
            'empty array' => [
                [],
                []
            ],
            'filled array' => [
                ['some value'],
                ['some value']
            ],
            'json' => [
                '{"json_object":"123ABC"}',
                ['json_object' => '123ABC']
            ]
        ];
    }

    /**
     * @param $response
     * @param $expected
     *
     * @throws \Exception
     *
     * @dataProvider formatResponseProvider
     */
    public function testFormatResponse($response, $expected)
    {
        $instance = $this->getInstance();
        $result = $this->invokeArgs('formatResponse', [$response], $instance);

        $this->assertEquals($expected, $result);
    }
}
