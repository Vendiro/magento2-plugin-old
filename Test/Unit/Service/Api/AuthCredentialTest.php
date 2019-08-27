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
namespace TIG\Vendiro\Test\Unit\Service\Api;

use Magento\Framework\Encryption\Encryptor;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Service\Api\AuthCredential;
use TIG\Vendiro\Test\TestCase;

class DataTest extends TestCase
{
    protected $instanceClass = AuthCredential::class;

    public function testGet()
    {
        $configurationMock = $this->getFakeMock(Configuration::class)->setMethods(['getKey', 'getToken'])->getMock();
        $configurationMock->expects($this->once())->method('getKey')->willReturn('a');
        $configurationMock->expects($this->once())->method('getToken')->willReturn('b');

        $encryptorMock = $this->getFakeMock(Encryptor::class)->setMethods(['decrypt'])->getMock();

        $encryptorMock
            ->expects($this->atLeastOnce())
            ->method('decrypt')
            ->willReturn('a', 'b');

        $instance = $this->getInstance(['configuration' => $configurationMock, 'encryptor' => $encryptorMock]);
        $result = $instance->get();

        $this->assertEquals('YTpi', $result);
    }
}
