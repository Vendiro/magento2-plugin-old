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
namespace TIG\Vendiro\Block\Adminhtml\Shipment\Options;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Store\Model\StoreManagerInterface;
use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use TIG\Vendiro\Model\ResourceModel\Carrier\CollectionFactory;

class Create extends Template implements BlockInterface
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var Configuration */
    private $configuration;

    /** @var CollectionFactory  */
    private $collectionFactory;

    /**
     * @param Template\Context          $context
     * @param CollectionFactory         $collectionFactory
     * @param Configuration             $configuration
     * @param StoreManagerInterface     $storeManager
     * @param array                     $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        Configuration $configuration,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        $collection = $this->collectionFactory;
        $collection = $collection->create();

        return $collection->getItems();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultCarrier()
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();

        return $this->configuration->getDefaultCarrier($storeId);
    }
}
