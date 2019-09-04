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
namespace TIG\Vendiro\Service\Inventory\ProductStock;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class SalableQty
{
    /** @var GetAssignedStockIdsBySku */
    private $getAssignedStockIdsBySku;

    /** @var GetProductSalableQtyInterface */
    private $getProductSalableQty;

    public function __construct(
        GetAssignedStockIdsBySku $getAssignedStockIdsBySku,
        GetProductSalableQtyInterface $getProductSalableQty
    ) {
        $this->getAssignedStockIdsBySku = $getAssignedStockIdsBySku;
        $this->getProductSalableQty = $getProductSalableQty;
    }

    /**
     * @param string $sku
     *
     * @return float|int
     * @throws InputException
     * @throws LocalizedException
     */
    public function getQtyBySku($sku)
    {
        $qty = 0;
        $stockIds = $this->getAssignedStockIdsBySku->execute($sku);

        if (count($stockIds) <= 0) {
            return $qty;
        }

        foreach ($stockIds as $stockId) {
            $qty += $this->getProductSalableQty->execute($sku, $stockId);
        }

        return $qty;
    }
}
