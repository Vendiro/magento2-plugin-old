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

interface OrderInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setOrderId($value);

    /**
     * @return int
     */
    public function getVendiroId();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setVendiroId($value);

    /**
     * @return int
     */
    public function getOrderRef();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setOrderRef($value);

    /**
     * @return string
     */
    public function getMarketplaceOrderId();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setMarketplaceOrderId($value);

    /**
     * @return string
     */
    public function getOrderDate();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setOrderDate($value);

    /**
     * @return string
     */
    public function getFulfilmentByMarketplace();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setFulfilmentByMarketplace($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getMarketplaceName();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setMarketplaceName($value);

    /**
     * @return string
     */
    public function getMarketplaceReference();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setMarketplaceReference($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getImportedAt();

    /**
     * @param $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function setImportedAt($value);
}
