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

use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Model\Order\Status\HistoryRepository;

class OrderStatusManager
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var HistoryRepository */
    private $historyRepository;

    /** @var Transaction */
    private $transaction;

    /** @var Log */
    private $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param HistoryRepository        $historyRepository
     * @param Transaction              $transaction
     * @param Log                      $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        HistoryRepository $historyRepository,
        Transaction $transaction,
        Log $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->historyRepository = $historyRepository;
        $this->transaction = $transaction;
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
     * @param int $orderId
     *
     * @return string|null
     */
    public function getIncrementId($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getIncrementId();
    }

    /**
     * @param int    $orderId
     * @param string $state
     */
    public function setNewState($orderId, $state)
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $orderConfig = $order->getConfig();
        $defaultStatus = $orderConfig->getStateDefaultStatus($state);

        $order->setState($state);
        $order->setStatus($defaultStatus);
        $order->addStatusToHistory($order->getStatus());

        $this->orderRepository->save($order);
    }

    /**
     * @param int $orderId
     */
    public function createInvoice($orderId)
    {
        /** @var OrderInterface|Order $order */
        $order = $this->orderRepository->get($orderId);

        if (!$order->canInvoice()) {
            return;
        }

        $this->registerInvoiceAndTransaction($order);
    }

    /**
     * @param OrderInterface|Order $order
     */
    private function registerInvoiceAndTransaction($order)
    {
        try {
            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $order->setIsInProcess(true);
            $invoice->save();

            $transaction = $this->transaction->addObject($invoice);
            $transaction->addObject($invoice->getOrder());
            $transaction->save();
        } catch (LocalizedException $exception) {
            $message = 'Could not create an invoice for order #' . $order->getId() . ': ' . $exception->getMessage();
            $this->logger->critical($message);
        } catch (\Exception $exception) {
            $message = 'Could not create an invoice for order #' . $order->getId() . ': ' . $exception->getMessage();
            $this->logger->critical($message);
        }
    }
}
