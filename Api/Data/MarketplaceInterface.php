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
namespace TIG\Vendiro\Api\Data;

interface MarketplaceInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \TIG\Vendiro\Api\Data\MarketplaceInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getMarketplaceId();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\MarketplaceInterface
     */
    public function setMarketplaceId($value);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\MarketplaceInterface
     */
    public function setCountryCode($value);

    /**
     * @param $value
     *
     * @return string
     */
    public function getCurrency($value);

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\MarketplaceInterface
     */
    public function setCurrency($value);

    /**
     * @param $value
     *
     * @return string
     */
    public function getName($value);

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\MarketplaceInterface
     */
    public function setName($value);
}
