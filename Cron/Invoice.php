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
namespace TIG\Vendiro\Cron;

use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Invoice\Data as InvoiceService;

class Invoice
{
    /** @var ApiConfiguration */
    private $apiConfiguration;

    /** @var InvoiceService $invoiceService */
    private $invoiceService;

    /**
     * @param ApiConfiguration $apiConfiguration
     * @param InvoiceService   $invoiceService
     */
    public function __construct(
        ApiConfiguration $apiConfiguration,
        InvoiceService $invoiceService
    ) {
        $this->apiConfiguration = $apiConfiguration;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Upload Magento Invoices to Vendiro
     */
    public function sendInvoices()
    {
        if (!$this->apiConfiguration->canSendInvoices()) {
            return;
        }

        $this->invoiceService->sendInvoice();
    }
}
