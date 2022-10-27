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
namespace TIG\Vendiro\Service\Software;

use Magento\Framework\App\ProductMetadataInterface;

class Data
{
    /** Module code */
    const MODULE_CODE = 'TIG_Vendiro';

    /** Version of Module */
    const VENDIRO_VERSION = '1.5.0-RC1';

    /** @var array */
    private $phpVersionSupport = [
        '2.3' => ['7.3' => ['+'], '7.4' => ['+']],
        '2.4' => ['7.3' => ['+'], '7.4' => ['+'], '8.1' => ['+']]
    ];

    /** @var ProductMetadataInterface */
    private $productMetaData;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetaData = $productMetadata;
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return self::VENDIRO_VERSION;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return self::MODULE_CODE;
    }

    /**
     * @return bool|int
     */
    public function phpVersionIsSupported()
    {
        $magentoVersion = $this->getMagentoVersionArray();
        $phpVersion     = $this->getPhpVersionArray();

        if (!is_array($magentoVersion) || !is_array($phpVersion)) {
            return - 1;
        }

        $currentVersion = $this->getCurrentVersion($magentoVersion, $phpVersion);

        if (!$currentVersion) {
            return - 1;
        }

        return $this->comparePhpVersion((int)$phpVersion[2], $currentVersion);
    }

    /**
     * @param $magentoVersion
     * @param $phpVersion
     *
     * @return bool|string
     */
    private function getCurrentVersion($magentoVersion, $phpVersion)
    {
        $currentVersion = false;

        $magentoMajorMinor = $magentoVersion[0] . '.' . $magentoVersion[1];
        $phpMajorMinor     = $phpVersion[0] . '.' . $phpVersion[1];

        if (!isset($this->phpVersionSupport[$magentoMajorMinor])
            || !isset($this->phpVersionSupport[$magentoMajorMinor][$phpMajorMinor])) {
            return $currentVersion;
        }

        $currentVersion = $this->phpVersionSupport[$magentoMajorMinor][$phpMajorMinor];

        return $currentVersion;
    }

    /**
     * @return bool|string
     */
    public function getPhpVersion()
    {
        $version = false;

        if (function_exists('phpversion')) {
            $version = phpversion();
        }

        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        }

        return $version;
    }

    /**
     * @return array|bool
     */
    public function getMagentoVersion()
    {
        $magentoVersion = $this->getMagentoVersionArray();

        if (is_array($magentoVersion)) {
            return $magentoVersion[0] . '.' . $magentoVersion[1];
        }

        return false;
    }

    /**
     * @param $phpPatch
     * @param $currentVersion
     *
     * @return bool
     */
    private function comparePhpVersion($phpPatch, $currentVersion)
    {
        $return = false;

        if (in_array($phpPatch, $currentVersion)
            || (in_array('+', $currentVersion) && $phpPatch >= max($currentVersion))
        ) {
            $return = true;
        }

        return $return;
    }

    /**
     * @return array|bool|string
     */
    private function getPhpVersionArray()
    {
        $version = $this->getPhpVersion();
        $version = explode('.', $version);

        return $version;
    }

    /**
     * @return array|bool
     */
    private function getMagentoVersionArray()
    {
        $version        = false;
        $currentVersion = $this->productMetaData->getVersion();

        if (isset($currentVersion)) {
            $version = explode('.', $currentVersion);
        }

        return $version;
    }
}
