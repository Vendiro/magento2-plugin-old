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
namespace TIG\Vendiro\Service\Order;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\OrderRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Order\Status\HistoryRepository;

class OrderStatusManager
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var HistoryRepository */
    private $historyRepository;

    /** @var Log */
    private $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param HistoryRepository        $historyRepository
     * @param Log                      $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        HistoryRepository $historyRepository,
        Log $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->historyRepository = $historyRepository;
        $this->logger = $logger;
    }

    /**
     * @param int    $orderId
     * @param string $comment
     */
    public function addHistoryComment($orderId, $comment)
    {
        $order = $this->orderRepository->get($orderId);
        $orderStatus = $order->getStatus();

        $orderHistoryComment = $this->historyRepository->create();
        $orderHistoryComment->setComment($comment);
        $orderHistoryComment->setParentId($orderId);
        $orderHistoryComment->setStatus($orderStatus);
        $orderHistoryComment->setEntityName('order');

        try {
            $this->historyRepository->save($orderHistoryComment);
        } catch (CouldNotSaveException $exception) {
            $this->logger->critical('Vendiro add history comment went wrong: ' . $exception->getMessage());
        }
    }

    /**
     * @param int    $orderId
     * @param string $state
     */
    public function setNewState($orderId, $state)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        $orderConfig = $order->getConfig();
        $defaultStatus = $orderConfig->getStateDefaultStatus($state);

        $order->setState($state);
        $order->setStatus($defaultStatus);
        $order->addStatusToHistory($order->getStatus());

        $this->orderRepository->save($order);
    }
}
