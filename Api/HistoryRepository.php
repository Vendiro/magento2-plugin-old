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

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\Data\OrderStatusHistorySearchResultInterfaceFactory;
use Magento\Sales\Model\Spi\OrderStatusHistoryResourceInterface;

class HistoryRepository extends \Magento\Sales\Model\Order\Status\HistoryRepository
{
    private $historyFactory;

    public function __construct(
        OrderStatusHistoryResourceInterface $historyResource,
        OrderStatusHistoryInterfaceFactory $historyFactory,
        OrderStatusHistorySearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        parent::__construct($historyResource, $historyFactory, $searchResultFactory, $collectionProcessor);

        $this->historyFactory = $historyFactory;
    }

    /**
     * @param array $data
     *
     * @return \Magento\Sales\Api\Data\OrderStatusHistoryInterface
     */
    public function create(array $data = [])
    {
        return $this->historyFactory->create($data);
    }
}