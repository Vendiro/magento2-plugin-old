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
namespace TIG\Vendiro\Controller\Adminhtml\Config\Stock;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use TIG\Vendiro\Service\Inventory\QueueAll as QueueAllService;

class QueueAll extends Action
{
    /** @var QueueAllService */
    private $queueAllService;

    /**
     * @param Action\Context  $context
     * @param QueueAllService $queueAllService
     */
    public function __construct(
        Action\Context $context,
        QueueAllService $queueAllService
    ) {
        parent::__construct($context);

        $this->queueAllService = $queueAllService;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $result = [
            'error' => true,
            //@codingStandardsIgnoreLine
            'message' => __("The products could not be queued for updating the stock at Vendiro.")
        ];

        $queueingResult = $this->queueAllService->queueAll();

        if ($queueingResult) {
            $message = 'Your products have been successfully queued and their stock will be send to Vendiro soon. '
                . 'Depending on your amount of products, this may take a few minutes up to a few hours.';

            //@codingStandardsIgnoreLine
            $result['message'] = __($message);
            $result['error'] = false;
        }

        $response = $this->getResponse();
        return $response->representJson(\Zend_Json::encode($result));
    }
}
