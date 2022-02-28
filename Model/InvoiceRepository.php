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
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\Vendiro\Api\Data\InvoiceInterface;
use TIG\Vendiro\Api\InvoiceRepositoryInterface;
use TIG\Vendiro\Model\ResourceModel\Invoice\CollectionFactory;

class InvoiceRepository extends AbstractRepository implements InvoiceRepositoryInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @var InvoiceFactory $invoiceFactory
     */
    private $invoiceFactory;

    /**
     * InvoiceRepository constructor.
     *
     * @param ScopeConfigInterface              $scopeConfig
     * @param SearchResultsInterfaceFactory     $searchResultsFactory
     * @param SearchCriteriaBuilder             $searchCriteriaBuilder
     * @param InvoiceFactory                    $invoiceFactory
     * @param CollectionFactory                 $collectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        InvoiceFactory $invoiceFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->invoiceFactory = $invoiceFactory;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     * @throws CouldNotSaveException
     */
    public function save(InvoiceInterface $invoice)
    {
        try {
            $invoice->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $invoice;
    }

    /**
     * @param $entityId
     *
     * @return InvoiceInterface
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        $invoice = $this->invoiceFactory->create();
        $invoice->load($entityId);

        if (!$invoice->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Invoice with id "%1" does not exist.', $entityId));
        }

        return $invoice;
    }

    /**
     * @param array $data
     *
     * @return Invoice
     */
    public function create(array $data = [])
    {
        return $this->invoiceFactory->create($data);
    }
}
