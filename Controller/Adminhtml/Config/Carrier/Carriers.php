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
namespace TIG\Vendiro\Controller\Adminhtml\Config\Carrier;

use Magento\Backend\App\Action;
use TIG\Vendiro\Service\Carrier\Data;

class Carriers extends Action
{
    /** @var Data */
    private $data;

    public function __construct(
        Action\Context $context,
        Data $data
    ) {
        parent::__construct($context);

        $this->data = $data;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \TIG\Vendiro\Exception
     */
    public function execute()
    {
        $result = [
            'error' => true,
            //@codingStandardsIgnoreLine
            'message' => __('Your Vendiro shipment providers could not be retreived.')
        ];

        if ($this->updateCarriers()) {
            $result['error'] = false;
            //@codingStandardsIgnoreLine
            $result['message'] = __('Your Vendiro shipment providers are successfully updated. Save your changes and refresh the page.');
        }

        $response = $this->getResponse();
        return $response->representJson(\Zend_Json::encode($result));
    }

    /**
     * @return bool
     * @throws \TIG\Vendiro\Exception
     */
    private function updateCarriers()
    {
        $hasCarriers = false;

        $carriers = $this->data->updateCarriers();

        if ($carriers === null) {
            $hasCarriers = true;
        }

        return $hasCarriers;
    }
}
