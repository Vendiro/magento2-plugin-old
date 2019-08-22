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

    /** @var Encryptor */
    private $encryptor;

    public function __construct(Configuration $configuration, Encryptor $encryptor)
    {
        $this->configuration = $configuration;
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function get()
    {
        $authKey = $this->configuration->getKey();
        $authKey = $this->encryptor->decrypt($authKey);

        $authToken = $this->configuration->getToken();
        $authToken = $this->encryptor->decrypt($authToken);

        $authString = $authKey . ':' . $authToken;
        $authCredential = base64_encode($authString);

        return $authCredential;
    }
}
