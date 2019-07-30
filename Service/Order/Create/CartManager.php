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
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use TIG\Vendiro\Api\HistoryRepository;
use TIG\Vendiro\Logging\Log;

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

    /** @var HistoryRepository $historyRepository */
    private $historyRepository;

    /**
     * @param StoreManagerInterface   $storeManager
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     * @param Log                     $logger
     * @param HistoryRepository       $historyRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository,
        Log $logger,
        HistoryRepository $historyRepository
    ) {
        $this->storeManager = $storeManager;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param string $storeCode
     */
    public function createCart($storeCode = 'default')
    {
        try {
            $store = $this->storeManager->getStore($storeCode);

            $cartId = $this->cartManagement->createEmptyCart();
            $this->cart = $this->cartRepository->get($cartId);
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
            return;
        }

        $this->cart->setStoreId($store->getId());
        $this->cart->setCurrency(); //Interface implement to set currency?
        $this->cart->setCheckoutMethod(CartManagementInterface::METHOD_GUEST);
    }

    /**
     * @param ProductInterface $product
     * @param $quantity
     */
    public function addProduct($product, $quantity)
    {
        try {
            $this->cart->addProduct($product, $quantity);
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
            $payment->importData(['method' => $method]); //Use Vendiro payment method?
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }
    }

    /**
     * @return int
     */
    public function placeOrder()
    {
        $orderId = false;
        $this->cart->collectTotals();
        $this->cartRepository->save($this->cart);

        try {
            $this->cart = $this->cartRepository->get($this->cart->getId());
            $orderId = $this->cartManagement->placeOrder($this->cart->getId());
        } catch (LocalizedException $exception) {
            $this->logger->critical('Vendiro import went wrong: ' . $exception->getMessage());
        }

        $orderHistoryComment = $this->historyRepository->create();
        $orderHistoryComment->setComment('Imported from Vendiro');
        $orderHistoryComment->setParentId($orderId);
        $orderHistoryComment->setStatus('pending');
        $orderHistoryComment->setEntityName('order');
        $this->historyRepository->save($orderHistoryComment);

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
