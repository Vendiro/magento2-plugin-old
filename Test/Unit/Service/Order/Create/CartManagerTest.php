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
namespace TIG\Vendiro\Test\Unit\Service\Order\Create;

use TIG\Vendiro\Service\Order\Create\CartManager;
use TIG\Vendiro\Test\TestCase;

class CartManagerTest extends TestCase
{
    protected $instanceClass = CartManager::class;

    public function testFormatAddress()
    {
        $apiAddress = [
            'name' => 'Support',
            'name2' => 'TIG',
            'lastname' => 'Servicedesk',
            'street' => 'Kabelweg 37',
            'street2' => 'D',
            'city' => 'Amsterdam',
            'postalcode' => '1014BA',
            'country' => 'NL',
            'phone' => '0201122233',
            'email' => 'test@email.com'
        ];

        $expectedAddress = [
            'firstname' => 'Support',
            'lastname' => 'Servicedesk',
            'company' => 'TIG',
            'street' => ['Kabelweg 37', 'D'],
            'city' => 'Amsterdam',
            'country_id' => 'NL',
            'postcode' => '1014BA',
            'telephone' => '0201122233',
            'email' => 'test@email.com',
        ];

        $instance = $this->getInstance();
        $result = $this->invokeArgs('formatAddress', [$apiAddress], $instance);

        $this->assertEquals($expectedAddress, $result);
    }
}
