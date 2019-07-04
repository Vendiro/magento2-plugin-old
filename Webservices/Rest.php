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
use TIG\Vendiro\Webservices\Endpoints\EndpointInterface;

class Rest
{
    /** @var ZendClient */
    private $zendClient;

    /** @var ApiConfiguration */
    private $apiConfiguration;

    /**
     * @param ZendClient       $zendClient
     * @param ApiConfiguration $apiConfiguration
     */
    public function __construct(
        ZendClient $zendClient,
        ApiConfiguration $apiConfiguration
    ) {
        $this->zendClient = $zendClient;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * @param EndpointInterface $endpoint
     *
     * @return array|\Zend_Http_Response
     * @throws \Zend_Http_Client_Exception
     */
    public function getRequest(EndpointInterface $endpoint)
    {
        $this->setUri($endpoint->getEndpointUrl());
        $this->setHeaders();
        $this->setParameters($endpoint);

        try {
            //TODO: possible response convert?
            $response = $this->zendClient->request();
        } catch (\Zend_Http_Client_Exception $e) {
            $response = [
                'success' => false,
                'error' => __('%1 : Zend Http Client exception', $e->getCode())
            ];
        }

        return $response;
    }

    /**
     * @param string $endpointUrl
     *
     * @throws \Zend_Http_Client_Exception
     */
    private function setUri($endpointUrl)
    {
        $uri = $this->apiConfiguration->getModusApiBaseUrl() . $endpointUrl;

        $this->zendClient->setUri($uri);
    }

    /**
     * @throws \Zend_Http_Client_Exception
     */
    private function setHeaders()
    {
        $this->zendClient->setHeaders([
            'Authorization' => 'Basic ' . $this->apiConfiguration->getAuthCredentials(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8'
        ]);
    }

    /**
     * @param EndpointInterface $endpoint
     *
     * @throws \Zend_Http_Client_Exception
     */
    private function setParameters(EndpointInterface $endpoint)
    {
        $endpointMethod = $endpoint->getMethod();
        $endpointData = $endpoint->getRequestData();

        $this->zendClient->setMethod($endpointMethod);

        if (empty($endpointData)) {
            return;
        }

        switch ($endpointMethod) {
            case ZendClient::GET:
                $this->zendClient->setParameterGet($endpointData);
                break;
            case ZendClient::POST:
            case ZendClient::PUT:
            default:
                $this->zendClient->setRawData(json_encode($endpointData), 'application/json');
                break;
        }
    }
}
