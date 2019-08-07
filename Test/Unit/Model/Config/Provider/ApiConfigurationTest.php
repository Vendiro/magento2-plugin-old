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
namespace TIG\Vendiro\Test\Unit\Model\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Test\TestCase;

class ApiConfigurationTest extends TestCase
{
    protected $instanceClass = ApiConfiguration::class;

    public function testGetLiveApiBaseUrl()
    {
        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('tig_vendiro/endpoints/api_base_url')
            ->willReturn('some url');

        $instance = $this->getInstance(['scopeConfig' => $scopeConfigMock]);
        $result = $instance->getLiveApiBaseUrl();
        $this->assertEquals('some url', $result);
    }

    public function testGetTestApiBaseUrl()
    {
        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('tig_vendiro/endpoints/test_api_base_url')
            ->willReturn('some test url');

        $instance = $this->getInstance(['scopeConfig' => $scopeConfigMock]);
        $result = $instance->getTestApiBaseUrl();
        $this->assertEquals('some test url', $result);
    }

    /**
     * @return array
     */
    public function vendiroConfigProvider()
    {
        return [
            'extension enabled and vendiro config enabled' => [
                true,
                true,
                true
            ],
            'extension disabled and vendiro config enabled' => [
                false,
                true,
                false
            ],
            'extension enabled and vendiro config disabled' => [
                true,
                false,
                false
            ],
            'extension disabled and vendiro config disabled' => [
                false,
                false,
                false
            ],
        ];
    }

    /**
     * @param $extensionEnabled
     * @param $configEnabled
     * @param $expect
     *
     * @dataProvider vendiroConfigProvider
     */
    public function testCanImportOrders($extensionEnabled, $configEnabled, $expect)
    {
        $vendiroConfigMock = $this->getFakeMock(Configuration::class)
            ->setMethods(['isEnabled', 'isOrderImportEnabled'])
            ->getMock();
        $vendiroConfigMock->expects($this->once())->method('isEnabled')->willReturn($extensionEnabled);
        $vendiroConfigMock->expects($this->exactly((int)$extensionEnabled))
            ->method('isOrderImportEnabled')
            ->willReturn($configEnabled);

        $instance = $this->getInstance(['configuration' => $vendiroConfigMock]);
        $result = $instance->canImportOrders();

        $this->assertEquals($expect, $result);
    }

    /**
     * @param $extensionEnabled
     * @param $configEnabled
     * @param $expect
     *
     * @dataProvider vendiroConfigProvider
     */
    public function testCanRegisterShipments($extensionEnabled, $configEnabled, $expect)
    {
        $vendiroConfigMock = $this->getFakeMock(Configuration::class)
            ->setMethods(['isEnabled', 'isRegisterShipmentEnabled'])
            ->getMock();
        $vendiroConfigMock->expects($this->once())->method('isEnabled')->willReturn($extensionEnabled);
        $vendiroConfigMock->expects($this->exactly((int)$extensionEnabled))
            ->method('isRegisterShipmentEnabled')
            ->willReturn($configEnabled);

        $instance = $this->getInstance(['configuration' => $vendiroConfigMock]);
        $result = $instance->canRegisterShipments();

        $this->assertEquals($expect, $result);
    }

    /**
     * @param $extensionEnabled
     * @param $configEnabled
     * @param $expect
     *
     * @dataProvider vendiroConfigProvider
     */
    public function testCanUpdateInventory($extensionEnabled, $configEnabled, $expect)
    {
        $vendiroConfigMock = $this->getFakeMock(Configuration::class)
            ->setMethods(['isEnabled', 'isUpdateInventoryEnabled'])
            ->getMock();
        $vendiroConfigMock->expects($this->once())->method('isEnabled')->willReturn($extensionEnabled);
        $vendiroConfigMock->expects($this->exactly((int)$extensionEnabled))
            ->method('isUpdateInventoryEnabled')
            ->willReturn($configEnabled);

        $instance = $this->getInstance(['configuration' => $vendiroConfigMock]);
        $result = $instance->canUpdateInventory();

        $this->assertEquals($expect, $result);
    }
}
