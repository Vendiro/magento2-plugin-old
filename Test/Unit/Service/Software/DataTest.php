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
namespace TIG\Vendiro\Test\Unit\Service\Software;

use Magento\Framework\App\ProductMetadataInterface;
use TIG\Vendiro\Service\Software\Data;
use TIG\Vendiro\Test\TestCase;

class DataTest extends TestCase
{
    protected $instanceClass = Data::class;

    public function testGetModuleName()
    {
        $instance = $this->getInstance();
        $result = $instance->getModuleName();

        $this->assertEquals('TIG_Vendiro', $result);
    }

    /**
     * @return array
     */
    public function getCurrentVersionProvider()
    {
        return [
            'not supported magento version' => [
                ['2', '1'],
                ['7', '1'],
                false
            ],
            'not supported php version' => [
                ['2', '2'],
                ['5', '6'],
                false
            ],
            'supported magento and php versions' => [
                ['2', '3'],
                ['7', '3'],
                ['+']
            ],
        ];
    }

    /**
     * @param $magentoVersion
     * @param $phpVersion
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider getCurrentVersionProvider
     */
    public function testGetCurrentVersion($magentoVersion, $phpVersion, $expected)
    {
        $instance = $this->getInstance();
        $result = $this->invokeArgs('getCurrentVersion', [$magentoVersion, $phpVersion], $instance);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getMagentoVersionProvider()
    {
        return [
            'has no magento version' => [
                null,
                false
            ],
            'has magento version' => [
                '2.3.4',
                '2.3',
            ]
        ];
    }

    /**
     * @param $magentoVersion
     * @param $expected
     *
     * @throws \Exception
     * @dataProvider getMagentoVersionProvider
     */
    public function testGetMagentoVersion($magentoVersion, $expected)
    {
        $metaDataMock = $this->getFakeMock(ProductMetadataInterface::class)
            ->setMethods(['getVersion'])
            ->getMockForAbstractClass();
        $metaDataMock->expects($this->once())->method('getVersion')->willReturn($magentoVersion);

        $instance = $this->getInstance(['productMetaData' => $metaDataMock]);
        $result = $instance->getMagentoVersion();

        $this->assertEquals($expected, $result);
    }
}
