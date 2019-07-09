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

interface OrderRepositoryInterface
{
    /**
     * Save a Vendiro order to the queue
     *
     * @api
     * @param \TIG\Vendiro\Api\Data\OrderInterface $order
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function save(\TIG\Vendiro\Api\Data\OrderInterface $order);

    /**
     * Return a specific Vendiro order.
     *
     * @api
     * @param int $id
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    // @codingStandardsIgnoreLine
    public function getById($id);

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
     * @param \TIG\Vendiro\Api\Data\OrderInterface $order
     * @return bool
     */
    public function delete(\TIG\Vendiro\Api\Data\OrderInterface $order);

    /**
     * Delete a Vendiro order by Id.
     *
     * @api
     * @param int $id
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function deleteById($id);

    /**
     * Create a Vendiro order.
     *
     * @api
     *
     * @param array $data
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function create(array $data = []);

    /**
     * Get by field with value
     *
     * @api
     * @param string $field
     * @param string $value
     *
     * @return \TIG\Vendiro\Api\Data\OrderInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $id
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    // @codingStandardsIgnoreLine
    public function getByOrderId($id);
}
