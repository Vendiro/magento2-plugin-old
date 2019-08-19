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

use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use TIG\Vendiro\Api\CarrierRepositoryInterface;
use TIG\Vendiro\Api\Data\CarrierInterface;
use TIG\Vendiro\Model\ResourceModel\Carrier as CarrierResourceModel;
use TIG\Vendiro\Model\ResourceModel\Carrier\CollectionFactory;

class CarrierRepository extends AbstractRepository implements CarrierRepositoryInterface
{

    /** @var CarrierFactory $carrierFactory */
    private $carrierFactory;

    /** @var CarrierResourceModel $carrierResourceModel */
    private $carrierResourceModel;

    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CarrierFactory $carrierFactory,
        CarrierResourceModel $carrierResourceModel,
        CollectionFactory $carrierCollectionFactory
    ) {
        $this->carrierFactory = $carrierFactory;
        $this->carrierResourceModel = $carrierResourceModel;

        $this->setCollection($carrierCollectionFactory);
        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * Save the carrier
     *
     * @param CarrierInterface $carrier
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CarrierInterface $carrier)
    {
        try {
            $this->carrierResourceModel->save($carrier);
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

    /**
     * @param array $data
     *
     * @return Carrier
     */
    public function create(array $data = [])
    {
        return $this->carrierFactory->create($data);
    }
}
