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
namespace TIG\Vendiro\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Vendiro extends AbstractCarrier implements CarrierInterface
{
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_vendiro';

    /** @var ResultFactory */
    private $resultFactory;

    /** @var MethodFactory */
    private $methodFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $resultFactory,
        MethodFactory $methodFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->resultFactory = $resultFactory;
        $this->methodFactory = $methodFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function collectRates(RateRequest $request)
    {
        $title = $this->getConfigData('title');
        $name = $this->getConfigData('name');
        $code = $this->getCarrierCode();
        $amount = $this->getShippingCost($request);

        $result = $this->resultFactory->create();
        $method = $this->methodFactory->create();

        $method->setCarrier($code);
        $method->setCarrierTitle($title);
        $method->setMethod('shipping');
        $method->setMethodTitle($name);
        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     * @param RateRequest $request
     *
     * @return string|int|float
     */
    private function getShippingCost(RateRequest $request)
    {
        $quoteItem = $request->getAllItems()[0];
        $quote = $quoteItem->getQuote();
        $shippingCost = $quote->getVendiroShippingCost();

        return $shippingCost;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedMethods()
    {
        return ['tig_vendiro' => $this->getConfigData('name')];
    }
}
