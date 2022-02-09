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
use Magento\Framework\Exception\CouldNotSaveException;
use TIG\Vendiro\Api\Data\MarketplaceInterface;
use TIG\Vendiro\Api\MarketplaceRepositoryInterface;
use TIG\Vendiro\Model\ResourceModel\Marketplace as MarketplaceResourceModel;
use TIG\Vendiro\Model\ResourceModel\Marketplace\CollectionFactory;

class MarketplaceRepository extends AbstractRepository implements MarketplaceRepositoryInterface
{
    /** @var MarketplaceFactory $marketplaceFactory */
    private $marketplaceFactory;

    /** @var MarketplaceResourceModel $marketplaceResourceModel */
    private $marketplaceResourceModel;

    /**
     * MarketplaceRepository constructor.
     *
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param MarketplaceFactory            $marketplaceFactory
     * @param MarketplaceResourceModel      $marketplaceResourceModel
     * @param CollectionFactory             $collectionFactory
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MarketplaceFactory $marketplaceFactory,
        MarketplaceResourceModel $marketplaceResourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->marketplaceFactory       = $marketplaceFactory;
        $this->marketplaceResourceModel = $marketplaceResourceModel;
        $this->collectionFactory        = $collectionFactory;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * Save the marketplace
     *
     * @param MarketplaceInterface $marketplace
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(MarketplaceInterface $marketplace)
    {
        try {
            $this->marketplaceResourceModel->save($marketplace);
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

    /**
     * @param array $data
     *
     * @return Marketplace
     */
    public function create(array $data = [])
    {
        return $this->marketplaceFactory->create($data);
    }

    /**
     * @return array|null
     */
    public function getDuplicateMarketplaces($marketplaceIds)
    {
        return $this->getByFieldWithValue('marketplace_id', $marketplaceIds, 0, 'in');
    }
}
