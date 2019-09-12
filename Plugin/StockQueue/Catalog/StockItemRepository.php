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
namespace TIG\Vendiro\Plugin\StockQueue\Catalog;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Inventory\StockQueue;

class StockItemRepository
{
    /** @var RequestInterface */
    private $request;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var StockQueue */
    private $stockQueue;

    /** @var Log */
    private $logger;

    /**
     * @param RequestInterface           $request
     * @param ApiConfiguration           $apiConfiguration
     * @param ProductRepositoryInterface $productRepository
     * @param StockQueue                 $stockQueue
     * @param Log                        $logger
     */
    public function __construct(
        RequestInterface $request,
        ApiConfiguration $apiConfiguration,
        ProductRepositoryInterface $productRepository,
        StockQueue $stockQueue,
        Log $logger
    ) {
        $this->request = $request;
        $this->apiConfiguration = $apiConfiguration;
        $this->productRepository = $productRepository;
        $this->stockQueue = $stockQueue;
        $this->logger = $logger;
    }

    /**
     * @param StockItemRepositoryInterface $subject
     * @param                              $result
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterSave(StockItemRepositoryInterface $subject, $result)
    {
        if (!$this->apiConfiguration->canUpdateInventory() || $this->request->getParam('tig_vendiro_products_queued')) {
            return $result;
        }

        try {
            $productId = $result->getProductId();
            $magentoProduct = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            $errorMessage = 'Product could not be found for Product #' . $productId . ': ' . $exception->getMessage();
            $this->logger->critical($errorMessage);

            return $result;
        }

        $this->stockQueue->saveOrUpdateQueueBySku($magentoProduct->getSku());

        $this->setRequestProductsQueued();

        return $result;
    }

    private function setRequestProductsQueued()
    {
        $params = $this->request->getParams();
//        $params['tig_vendiro_products_queued'] = true;
        $this->request->setParams($params);
    }
}
