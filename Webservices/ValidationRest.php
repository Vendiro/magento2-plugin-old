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
namespace TIG\Vendiro\Webservices;

use Magento\Framework\HTTP\ZendClient;
use TIG\Vendiro\Model\Config\Provider\ApiConfiguration;
use TIG\Vendiro\Service\Api\AuthCredential;
use TIG\Vendiro\Service\Software\Data as SoftwareData;
use Magento\Framework\App\RequestInterface;

class ValidationRest extends AbstractRest
{

    private $request;

    /**
     * @param ZendClient       $zendClient
     * @param ApiConfiguration $apiConfiguration
     * @param AuthCredential   $authCredential
     * @param SoftwareData     $softwareData
     * @param RequestInterface $request
     */
    public function __construct(
        ZendClient $zendClient,
        ApiConfiguration $apiConfiguration,
        AuthCredential $authCredential,
        SoftwareData $softwareData,
        RequestInterface $request
    ) {
        $this->request = $request;
        parent::__construct($zendClient, $apiConfiguration, $authCredential, $softwareData);
    }

    /**
     * @throws \Zend_Http_Client_Exception
     */
    //@codingStandardsIgnoreLine
    protected function setHeaders()
    {
        $authKey = $this->getAuthParam('api_key');
        $authToken = $this->getAuthParam('api_token');

        $this->zendClient->setHeaders([
            'Authorization' => 'Basic ' . $this->authCredential->get($authKey, $authToken),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8',
            'User-Agent' => 'VendiroMagento2Plugin/' . $this->softwareData->getModuleVersion()
        ]);
    }

    /**
     * @param $parameter
     * @return null|string
     */
    private function getAuthParam($parameter = null)
    {
        if (!$parameter) {
            return null;
        }
        $post = $this->request->getParams();

        if (isset($post) &&
            isset($post[$parameter]) && !empty($post[$parameter]) &&
            $post[$parameter] != '******') {
            return $post[$parameter];
        }

        return null;
    }
}
