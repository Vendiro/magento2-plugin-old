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

namespace TIG\Vendiro\Service\Inventory;

use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Webservices\Endpoints\UpdateProductsStock;

class Data
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var ProductStock */
    private $productStock;

    /** @var UpdateProductsStock */
    private $updateProductsStock;

    /** @var Log */
    private $logger;

    /**
     * @param ApiConfiguration    $apiConfiguration
     * @param ProductStock        $productStock
     * @param UpdateProductsStock $updateProductsStock
     * @param Log                 $logger
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        ProductStock $productStock,
        UpdateProductsStock $updateProductsStock,
        Log $logger
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->productStock = $productStock;
        $this->updateProductsStock = $updateProductsStock;
        $this->logger = $logger;
    }

    public function updateProductInventory()
    {
        if (!$this->apiConfiguration->canUpdateInventory()) {
            return;
        }

        //TODO: get item list by rule; either "cron last run" date or by queue

        $sku = '';
        $qty = $this->productStock->getStockBySku($sku);
        $requestData = [['sku' => $sku, 'stock' => $qty]];

        $this->updateProductsStock->setRequestData($requestData);

        $response = $this->updateProductsStock->call();

        if ((int)$response['count_invalid_skus'] > 0 && isset($response['invalid_skus'])) {
            $this->logInvalidSkus($response['invalid_skus']);
        }
    }

    private function logInvalidSkus($invalidSkus)
    {
        if (!is_array($invalidSkus)) {
            $invalidSkus = [$invalidSkus];
        }

        $invalidSkusString = implode(', ', $invalidSkus);
        $noticeString = sprintf(__("The inventory of some SKU's could not be updated: %s"), $invalidSkusString);

        $this->logger->notice($noticeString);
    }
}
