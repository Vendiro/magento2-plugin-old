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
namespace TIG\Vendiro\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use TIG\Vendiro\Webservices\Endpoints\GetAccount;

class TestApi extends Action
{
    /** @var GetAccount */
    private $getAccount;

    public function __construct(
        Action\Context $context,
        GetAccount $getAccount
    ) {
        parent::__construct($context);

        $this->getAccount = $getAccount;
    }

    public function execute()
    {
        $result = [
            'error' => true,
            'message' => 'Your API Credentials could not be validated'
        ];

        if ($this->validateAccount()) {
            $result['error'] = false;
            $result['message'] = 'Your API Credentials are successfully validated';
        }

        $response = $this->getResponse();
        return $response->representJson(\Zend_Json::encode($result));
    }

    /**
     * @return bool
     */
    private function validateAccount()
    {
        $hasAccount = false;
        $hasUser = false;

        $accountResult = $this->getAccount->call();

        if (isset($accountResult['account']) && !empty($accountResult['account'])) {
            $hasAccount = true;
        }

        if (isset($accountResult['user']) && !empty($accountResult['user'])) {
            $hasUser = true;
        }

        return $hasAccount && $hasUser;
    }
}
