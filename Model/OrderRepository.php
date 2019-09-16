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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Model\ResourceModel\Order\CollectionFactory;

class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
    const VENDIRO_NEW_ORDERS_LIMIT = 'tig_vendiro/new_orders_limit';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @var OrderFactory $orderFactory
     */
    private $orderFactory;

    /**
     * OrderRepository constructor.
     *
     * @param ScopeConfigInterface              $scopeConfig
     * @param SearchResultsInterfaceFactory     $searchResultsFactory
     * @param SearchCriteriaBuilder             $searchCriteriaBuilder
     * @param OrderFactory                      $orderFactory
     * @param CollectionFactory                 $collectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderFactory $orderFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderFactory = $orderFactory;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order)
    {
        try {
            $order->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $order;
    }

    /**
     * @param $entityId
     *
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        $order = $this->orderFactory->create();
        $order->load($entityId);

        if (!$order->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $entityId));
        }

        return $order;
    }

    /**
     * @param array $data
     *
     * @return Order
     */
    public function create(array $data = [])
    {
        return $this->orderFactory->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getByOrderId($orderId, $limit = 1)
    {
        return $this->getByFieldWithValue('order_id', $orderId, $limit);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlreadyInsertedOrders($orderIds, $limit = 999)
    {
        $insertedOrders = [];
        $list = $this->getByFieldInArray('vendiro_id', $orderIds, $limit);

        if (!is_array($list)) {
            return $insertedOrders;
        }

        foreach ($list as $dbOrder) {
            $insertedOrders[$dbOrder->getVendiroId()] = ([
                'order_id' => $dbOrder->getOrderId()
            ]);
        }

        return $insertedOrders;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewOrders()
    {
        return $this->getByFieldWithValue('order_id', true, 'null');
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OrderInterface $order)
    {
        try {
            $order->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete a Vendiro order by Id.
     *
     * @param int $entityId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        $order = $this->getById($entityId);

        return $this->delete($order);
    }
}
