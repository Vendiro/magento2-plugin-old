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
namespace TIG\Vendiro\Service\Order\Create;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use TIG\Vendiro\Logging\Log;
use TIG\Vendiro\Service\Order\ApiStatusManager;
use TIG\Vendiro\Exception as VendiroException;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;

//@codingStandardsIgnoreFile
class CartManager
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CartManagementInterface */
    private $cartManagement;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var Log */
    private $logger;

    /** @var CartInterface|\Magento\Quote\Model\Quote */
    private $cart;

    /** @var ApiStatusManager $apiStatusManager */
    private $apiStatusManager;

    /** @var QuoteItemQtyList $quoteItemQtyList */
    private $quoteItemQtyList;

    /**
     * @param StoreManagerInterface                       $storeManager
     * @param CartManagementInterface                     $cartManagement
     * @param CartRepositoryInterface                     $cartRepository
     * @param Log                                         $logger
     * @param ApiStatusManager                            $apiStatusManager
     * @param QuoteItemQtyList $quoteItemQtyList
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository,
        Log $logger,
        ApiStatusManager $apiStatusManager,
        QuoteItemQtyList $quoteItemQtyList
    ) {
        $this->storeManager = $storeManager;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->apiStatusManager = $apiStatusManager;
        $this->quoteItemQtyList = $quoteItemQtyList;
    }

    /**
     * @param string $storeCode
     *
     * @throws \TIG\Vendiro\Exception
     */
    public function createCart($storeCode = 'default')
    {
        try {
            $store = $this->storeManager->getStore($storeCode);

            $cartId = $this->cartManagement->createEmptyCart();
            $this->cart = $this->cartRepository->get($cartId);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());

            $errorMessage = __("The order could not be exported. The store that was requested wasn't found.");
            throw new \TIG\Vendiro\Exception($errorMessage);
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
            return;
        }

        $this->cart->setStoreId($store->getId());
        $this->cart->setCurrency(); //Interface implement to set currency?
        $this->cart->setCheckoutMethod(CartManagementInterface::METHOD_GUEST);

        return $this->cart;
    }

    /**
     * @param ProductInterface $product
     * @param $quantity
     */
    public function addProduct($product, $quantity)
    {
        try {
            $this->cart->addProduct($product, $quantity);

            $quoteItem = $this->cart->getItemByProduct($product);
            $quoteItem->setNoDiscount(1);
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }
    }

    /**
     * @param array  $data
     * @param string $type
     */
    public function addAddress($data, $type = 'Billing')
    {
        $addressMethod = 'get' . ucfirst($type) . 'Address';

        $formattedAddress = $this->formatAddress($data);

        $cartAddress = $this->cart->$addressMethod();
        $cartAddress->addData($formattedAddress);
    }

    /**
     * @param string           $method
     * @param string|int|float $shippingCost
     */
    public function setShippingMethod($method, $shippingCost)
    {
        $this->cart->setVendiroShippingCost($shippingCost);

        $cartShippingAddress = $this->cart->getShippingAddress();
        $cartShippingAddress->setCollectShippingRates(true);
        $cartShippingAddress->collectShippingRates();
        $cartShippingAddress->setShippingMethod($method);
    }

    /**
     * @param string $method
     */
    public function setPaymentMethod($method)
    {
        $this->cart->setPaymentMethod($method);
        $this->cart->setInventoryProcessed(false);
        $payment = $this->cart->getPayment();

        try {
            $payment->importData(['method' => $method]);
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }
    }

    /**
     * @param $vendiroId
     *
     * @return int
     * @throws VendiroException
     */
    public function placeOrder($vendiroId)
    {
        $orderId = false;
        $this->cart->collectTotals();
        $this->cartRepository->save($this->cart);

        try {
            // Magento 2.3.7 fix for double quantity
            // this check is because the method is removed in 2.4
            if (method_exists($this->quoteItemQtyList, 'clear')) {
                $this->quoteItemQtyList->clear();
            }
            $this->cart = $this->cartRepository->get($this->cart->getId());
            $orderId = $this->cartManagement->placeOrder($this->cart->getId());
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
            throw new VendiroException(__($exception->getMessage()));
        }

        return $orderId;
    }

    /**
     * @param array $address
     *
     * @return array
     */
    private function formatAddress(array $address)
    {
        $newAddress = [
            'firstname' => $address['name'],
            'lastname' => $address['lastname'],
            'company' => $address['name2'],
            'street' => [0 => $address['street'], 1 => $address['street2']],
            'city' => $address['city'],
            'country_id' => $address['country'],
            'postcode' => $address['postalcode'],
            'telephone' => $address['phone'],
            'email' => $address['email']
        ];

        return $newAddress;
    }
}
