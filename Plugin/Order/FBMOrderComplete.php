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
namespace TIG\Vendiro\Plugin\Order;

use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\Session\SessionManagerInterface as CoreSession;

use TIG\Vendiro\Model\Payment\Vendiro as VendiroPayment;


/**
 * Class FBMOrderComplete
 *
 * @package TIG\Vendiro\Plugin\Order
 */
class FBMOrderComplete
{

    /**
     * @var CoreSession
     */
    protected $_coreSession;

    /**
     * FBMOrderComplete constructor.
     * @param CoreSession $coreSession
     */
    public function __construct(
        CoreSession $coreSession
    ){
        $this->_coreSession = $coreSession;
    }

    /**
     * @return CoreSession
     */
    public function getCoreSession()
    {
        return $this->_coreSession;
    }

    /**
     * Vendiro imported orders with Fulfilment_by_marketplace = true are
     * directly set to the complete state & status to prevent
     * WMS/Backend systems to pick them up
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return Order
     */
    public function beforeSave($subject, $order)
    {
        if ($order->getPayment()->getMethod() == VendiroPayment::PAYMENT_CODE &&
            $this->getCoreSession()->getFulfilmentByMarketplace() == true)
        {

            $state = Order::STATE_COMPLETE;
            $orderConfig = $order->getConfig();
            $defaultStatus = $orderConfig->getStateDefaultStatus($state);

            $order->setState($state);
            $order->setStatus($defaultStatus);

            $this->getCoreSession()->unsFulfilmentByMarketplace();
        }
    }
}
