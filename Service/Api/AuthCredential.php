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
namespace TIG\Vendiro\Service\Api;

use TIG\Vendiro\Model\Config\Provider\General\Configuration;
use Magento\Framework\Encryption\Encryptor;

class AuthCredential
{
    /** @var Configuration */
    private $configuration;

    /** @var Encryptor $encryptor */
    private $encryptor;

    /**
     * AuthCredential constructor.
     * @param Configuration $configuration
     * @param Encryptor $encryptor
     */
    public function __construct(Configuration $configuration,
                                Encryptor $encryptor)
    {
        $this->configuration = $configuration;
        $this->encryptor     = $encryptor;
    }


    /**
     * @param null $authKey
     * @param null $authToken
     * @return string
     */
    public function get($authKey = null, $authToken = null)
    {
        if (!$authKey) {
            $authKey = $this->encryptor->decrypt($this->configuration->getKey());
        }

        if (!$authToken) {
            $authToken = $this->encryptor->decrypt($this->configuration->getToken());
        }

        $authCredential = $this->authEncode($authKey, $authToken);

        return $authCredential;
    }

    /**
     * @param $authKey
     * @param $authToken
     * @return string
     */
    public function authEncode($authKey, $authToken)
    {
        $authString = $authKey . ':' . $authToken;
        $authCredential = base64_encode($authString);

        return $authCredential;
    }
}
