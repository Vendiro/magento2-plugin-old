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

namespace TIG\Vendiro\Service\Marketplaces;

use TIG\Vendiro\Api\MarketplaceRepositoryInterface;
use TIG\Vendiro\Exception;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Webservices\Endpoints\GetMarketplaces;

class Data
{
    /** @var Log $logger */
    private $logger;

    /** @var GetMarketplaces $getCarriers */
    private $getMarketplaces;

    /** @var MarketplaceRepositoryInterface $marketplaceRepository */
    private $marketplaceRepositoryInterface;

    /**
     * @param Log                                         $logger
     * @param GetMarketplaces                             $getMarketplaces
     * @param MarketplaceRepositoryInterface              $marketplaceRepositoryInterface
     */
    public function __construct(
        Log $logger,
        GetMarketplaces $getMarketplaces,
        MarketplaceRepositoryInterface $marketplaceRepositoryInterface
    ) {
        $this->logger                         = $logger;
        $this->getMarketplaces                = $getMarketplaces;
        $this->marketplaceRepositoryInterface = $marketplaceRepositoryInterface;
    }

    /**
     * @throws Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function updateMarketplaces()
    {
        $marketplaces = $this->getMarketplaces();

        $duplicateMarketplaces = $this->getDuplicateMarketPlaces($marketplaces);

        foreach ($duplicateMarketplaces as $duplicateMarketplace) {
            $this->updateMarketplace($duplicateMarketplace, $marketplaces[$duplicateMarketplace->getMarketplaceId()]);
            unset($marketplaces[$duplicateMarketplace->getMarketPlaceId()]);
        }

        foreach ($marketplaces as $marketplace) {
            $this->saveMarketplace($marketplace);
        }
    }

    /**
     * @return array|bool|mixed|\Zend_Http_Response
     * @throws \Zend_Http_Client_Exception
     */
    public function getMarketplaces()
    {
        $requestData = ['limit' => 250];
        $this->getMarketplaces->setRequestData($requestData);
        $marketplaces = $this->getMarketplaces->call();

        if (array_key_exists('message', $marketplaces)) {
            return false;
        }

        return $marketplaces;
    }

    /**
     * @param $marketplaces
     *
     * @return array|null
     */
    public function getDuplicateMarketPlaces($marketplaces)
    {
        $marketplaceIds = [];
        array_push($marketplaceIds, array_keys($marketplaces));

        $duplicateMarketplaces = $this->marketplaceRepositoryInterface->getDuplicateMarketplaces($marketplaceIds);

        if ($duplicateMarketplaces === null) {
            $duplicateMarketplaces = [];
        }

        return $duplicateMarketplaces;
    }

    /**
     * @param $marketplace
     *
     * @throws Exception
     */
    private function saveMarketplace($marketplace)
    {
        try {
            $marketplaceModel = $this->marketplaceRepositoryInterface->create();
            $marketplaceModel->setMarketplaceId($marketplace['id']);
            $marketplaceModel->setCountryCode($marketplace['country_code']);
            $marketplaceModel->setCurrency($marketplace['currency']);
            $marketplaceModel->setName($marketplace['name']);
            $this->marketplaceRepositoryInterface->save($marketplaceModel);
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new Exception(__($exception->getMessage()));
        }
    }

    /**
     * @param $duplicateMarketplce
     * @param $marketplace
     */
    public function updateMarketplace($duplicateMarketplce, $marketplace)
    {
        if ($duplicateMarketplce->getMarketplace() != $marketplace) {
            $duplicateMarketplce->setMarketplace($marketplace);
            $this->marketplaceRepositoryInterface->save($duplicateMarketplce);
        }
    }
}
