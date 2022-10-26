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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Vendiro\Model;

use Magento\Framework\Model\AbstractModel;
use TIG\Vendiro\Api\Data\MarketplaceInterface;

class Marketplace extends AbstractModel implements MarketplaceInterface
{
    const FIELD_MARKETPLACE_ID         = 'marketplace_id';
    const FIELD_COUNTRY_CODE           = 'country_code';
    const FIELD_CURRENCY               = 'currency';
    const FIELD_NAME                   = 'name';
    const FIELD_ALLOWED_DOCUMENT_TYPES = 'allowed_document_types';

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('TIG\Vendiro\Model\ResourceModel\Marketplace');
    }

    /**
     * @return int
     */
    public function getMarketplaceId()
    {
        return $this->getData(self::FIELD_MARKETPLACE_ID);
    }

    /**
     * @param $value
     *
     * @return Marketplace
     */
    public function setMarketplaceId($value)
    {
        return $this->setData(self::FIELD_MARKETPLACE_ID, $value);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getData(self::FIELD_COUNTRY_CODE);
    }

    /**
     * @param $value
     *
     * @return Marketplace
     */
    public function setCountryCode($value)
    {
        return $this->setData(self::FIELD_COUNTRY_CODE, $value);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(self::FIELD_CURRENCY);
    }

    /**
     * @param $value
     *
     * @return MarketplaceInterface|Marketplace
     */
    public function setCurrency($value)
    {
        return $this->setData(self::FIELD_CURRENCY, $value);
    }

    /**
     * @param $value
     *
     * @return MarketplaceInterface|Marketplace
     */
    public function setName($value)
    {
        return $this->setData(self::FIELD_NAME, $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::FIELD_NAME);
    }

    /**
     * @param $value
     *
     * @return MarketplaceInterface|Marketplace
     */
    public function setAllowedDocumentTypes($value)
    {
        return $this->setData(self::FIELD_ALLOWED_DOCUMENT_TYPES, $value);
    }

    /**
     * @return string
     */
    public function getAllowedDocumentTypes()
    {
        return $this->getData(self::FIELD_ALLOWED_DOCUMENT_TYPES);
    }
}
