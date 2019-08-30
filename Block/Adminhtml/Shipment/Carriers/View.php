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
namespace TIG\Vendiro\Block\Adminhtml\Shipment\Carriers;

use Magento\Backend\Block\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use TIG\Vendiro\Block\Adminhtml\Shipment\Options\Create;

class View extends Template implements BlockInterface
{

    /** @var Create */
    private $create;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @param Template\Context                        $context
     * @param ShipmentRepositoryInterface             $shipmentRepository
     * @param SearchCriteriaBuilder                   $searchCriteriaBuilder
     * @param Create                                  $create
     * @param array                                   $data
     */
    public function __construct(
        Template\Context $context,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Create $create,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->create = $create;
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParams()['order_id'];
    }
}
