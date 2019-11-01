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
namespace TIG\Vendiro\Plugin\StockQueue\Sales;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Inventory\StockQueue;

class OrderRepository
{
    /** @var RequestInterface */
    private $request;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var StockQueue */
    private $stockQueue;

    /**
     * OrderRepository constructor.
     *
     * @param RequestInterface $request
     * @param ApiConfiguration $apiConfiguration
     * @param StockQueue       $stockQueue
     */
    public function __construct(
        RequestInterface $request,
        ApiConfiguration $apiConfiguration,
        StockQueue $stockQueue
    ) {
        $this->request = $request;
        $this->apiConfiguration = $apiConfiguration;
        $this->stockQueue = $stockQueue;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Order                    $result
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function afterSave(OrderRepositoryInterface $subject, $result)
    {
        if (!$this->apiConfiguration->canUpdateInventory() || $this->request->getParam('tig_vendiro_products_queued')) {
            return $result;
        }

        foreach ($result->getAllItems() as $product) {
            $this->queueProductItem($product);
        }

        $this->setRequestProductsQueued();

        return $result;
    }

    /**
     * @param OrderItemInterface $product
     */
    private function queueProductItem($product)
    {
        if (!$this->validateProduct($product)) {
            return;
        }

        $this->stockQueue->saveOrUpdateQueueBySku($product->getSku());
    }

    /**
     * @param OrderItemInterface $product
     *
     * @return bool
     */
    private function validateProduct($product)
    {
        if (empty($product)) {
            return false;
        }

        $hasParentId = $product->getParentItemId();
        $priceIsZero = $product->getRowTotalInclTax() == 0;
        $isBundle = $product->getProductType() == Type::TYPE_BUNDLE;

        return !($hasParentId || $priceIsZero || $isBundle);
    }

    private function setRequestProductsQueued()
    {
        $params = $this->request->getParams();
        //@codingStandardsIgnoreLine
//        $params['tig_vendiro_products_queued'] = true;
        $this->request->setParams($params);
    }
}
