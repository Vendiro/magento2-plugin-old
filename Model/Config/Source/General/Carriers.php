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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Vendiro\Model\Config\Source\General;

use Magento\Framework\Option\ArrayInterface;
use TIG\Vendiro\Model\CarrierRepository;

class Carriers implements ArrayInterface
{
    private $carrierRepository;

    /**
     * Carrier constructor.
     *
     * @param \TIG\Vendiro\Model\CarrierRepository $carrierRepository
     */
    public function __construct(CarrierRepository $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Return option array for the inventory mode.
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->carrierRepository->getCollection();
        $collection = $collection->create();

        // @codingStandardsIgnoreStart
        $options = [];

        foreach($collection->getItems() as $carrier)
        {
            array_push($options, ['value'=> $carrier->getEntityId(), 'label' => __($carrier->getCarrier())]);
        }

        // @codingStandardsIgnoreEnd
        return $options;
    }
}
