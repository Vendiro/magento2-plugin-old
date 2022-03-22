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
namespace TIG\Vendiro\Service\Invoice;

use Magento\Sales\Model\Order\Invoice as MagentoInvoice;
use Magento\Sales\Model\Order\Pdf\Invoice;
use TIG\Vendiro\Api\Data\OrderInterface;
use TIG\Vendiro\Api\OrderRepositoryInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Webservices\Endpoints\AddDocument;

class Data
{
    /** @var Log $logger */
    private $logger;

    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /** @var Validate */
    private $validate;

    /** @var Invoice */
    private $pdfInvoice;

    /** @var AddDocument */
    private $addDocument;

    /**
     * @param Log                      $logger
     * @param OrderRepositoryInterface $orderRepository
     * @param Validate                 $validate
     * @param Invoice                  $pdfInvoice
     * @param AddDocument              $addDocument
     */
    public function __construct(
        Log $logger,
        OrderRepositoryInterface $orderRepository,
        Validate $validate,
        Invoice $pdfInvoice,
        AddDocument $addDocument
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->validate = $validate;
        $this->pdfInvoice = $pdfInvoice;
        $this->addDocument = $addDocument;
    }

    /**
     * Check if any invoices can be send to Vendiro, and does so if there are any
     */
    public function sendInvoice()
    {
        $vendiroOrders = $this->orderRepository->getInvoicesToSend();

        if (!is_array($vendiroOrders) || empty($vendiroOrders)) {
            return;
        }

        /** @var OrderInterface $vendirOrder */
        foreach ($vendiroOrders as $vendirOrder) {
            $this->uploadInvoice($vendirOrder);
        }
    }

    /**
     * @param OrderInterface $vendiroOrder
     */
    private function uploadInvoice($vendiroOrder)
    {
        $invoice = $this->validate->getInvoice($vendiroOrder);

        if ($invoice === false) {
            return;
        }

        try {
            $requestData = $this->getInvoiceData($invoice);
        } catch (\Zend_Pdf_Exception $exception) {
            $this->logger->notice('Could not get Invoice PDF: ' . $exception->getMessage());
            return;
        }

        $this->addDocument->setRequestData($requestData);

        try {
            $result = $this->addDocument->call($vendiroOrder->getOrderId());
        } catch (\Zend_Http_Client_Exception $exception) {
            $errorMessage = 'Vendiro Add Document for order #' . $vendiroOrder->getEntityId() . ' went wrong: ';
            $this->logger->critical($errorMessage . $exception->getMessage());
            return;
        }

        if (!array_key_exists('message', $result)) {
            $vendiroOrder->setInvoiceSend(1);
            $this->orderRepository->save($vendiroOrder);
        }
    }

    /**
     * @param MagentoInvoice $invoice
     *
     * @return array
     * @throws \Zend_Pdf_Exception
     */
    private function getInvoiceData($invoice)
    {
        $pdf = $this->pdfInvoice->getPdf([$invoice])->render();
        $pdfEncoded = base64_encode($pdf);

        $requestData = [
            "reference" => $invoice->getIncrementId(),
            "type" => "invoice",
            "data" => $pdfEncoded,
            "total_value" => $invoice->getGrandTotal(),
            "vat_value" => $invoice->getTaxAmount()
        ];

        return $requestData;
    }
}
