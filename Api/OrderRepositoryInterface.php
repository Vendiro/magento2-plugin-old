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
namespace TIG\Vendiro\Api;

use TIG\Vendiro\Api\Data\OrderInterface;

interface OrderRepositoryInterface
{
    /**
     * Save a Vendiro order to the queue
     *
     * @api
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function save(OrderInterface $order);

    /**
     * Return a specific Vendiro order.
     *
     * @api
     * @param int $entityId
     * @return OrderInterface
     */
    public function getById($entityId);

    /**
     * Retrieve a list of Vendiro orders.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific Venduro order.
     *
     * @api
     * @param OrderInterface $order
     * @return bool
     */
    public function delete(OrderInterface $order);

    /**
     * Delete a Vendiro order by Id.
     *
     * @api
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * Create a Vendiro order.
     *
     * @api
     *
     * @param array $data
     * @return OrderInterface
     */
    public function create(array $data = []);

    /**
     * Get by field with value
     *
     * @api
     * @param string $field
     * @param string $value
     *
     * @return OrderInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $orderId
     * @param int $limit
     *
     * @return OrderInterface|array|null
     */
    public function getByOrderId($orderId, $limit = 1);

    /**
     * @param     $status
     * @param int $limit
     *
     * @return array|OrderInterface|null
     */
    public function getByStatus($status, $limit = 10);

    /**
     * @param array $orderIds
     * @param int   $limit
     *
     * @return array
     */
    public function getAlreadyInsertedOrders($orderIds, $limit = 999);
}
