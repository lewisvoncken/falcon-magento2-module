<?php

namespace Deity\MagentoApi\Model\Api\Data;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class StockItem extends AbstractExtensibleModel implements StockItemInterface
{
    const FIELDS = [
        StockItemInterface::ITEM_ID,
        StockItemInterface::PRODUCT_ID,
        StockItemInterface::STOCK_ID,
        StockItemInterface::QTY,
        StockItemInterface::IS_QTY_DECIMAL,
        StockItemInterface::SHOW_DEFAULT_NOTIFICATION_MESSAGE,
        StockItemInterface::USE_CONFIG_MIN_QTY,
        StockItemInterface::MIN_QTY,
        StockItemInterface::USE_CONFIG_MIN_SALE_QTY,
        StockItemInterface::MIN_SALE_QTY,
        StockItemInterface::USE_CONFIG_MAX_SALE_QTY,
        StockItemInterface::MAX_SALE_QTY,
        StockItemInterface::USE_CONFIG_BACKORDERS,
        StockItemInterface::BACKORDERS,
        StockItemInterface::USE_CONFIG_NOTIFY_STOCK_QTY,
        StockItemInterface::NOTIFY_STOCK_QTY,
        StockItemInterface::USE_CONFIG_QTY_INCREMENTS,
        StockItemInterface::QTY_INCREMENTS,
        StockItemInterface::USE_CONFIG_ENABLE_QTY_INC,
        StockItemInterface::ENABLE_QTY_INCREMENTS,
        StockItemInterface::USE_CONFIG_MANAGE_STOCK,
        StockItemInterface::MANAGE_STOCK,
        StockItemInterface::IS_IN_STOCK,
        StockItemInterface::LOW_STOCK_DATE,
        StockItemInterface::IS_DECIMAL_DIVIDED,
        StockItemInterface::STOCK_STATUS_CHANGED_AUTO
    ];

    /**
     * @return int|null
     */
    public function getItemId()
    {
        return $this->_getData(StockItemInterface::ITEM_ID);
    }

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        return $this->setData(StockItemInterface::ITEM_ID, $itemId);
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return $this->_getData(StockItemInterface::PRODUCT_ID);
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(StockItemInterface::PRODUCT_ID, $productId);
    }

    /**
     * Retrieve stock identifier
     *
     * @return int|null
     */
    public function getStockId()
    {
        return $this->_getData(StockItemInterface::STOCK_ID);
    }

    /**
     * Set stock identifier
     *
     * @param int $stockId
     * @return $this
     */
    public function setStockId($stockId)
    {
        return $this->setData(StockItemInterface::STOCK_ID, $stockId);
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->_getData(StockItemInterface::QTY);
    }

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->setData(StockItemInterface::QTY, $qty);
    }

    /**
     * Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        return $this->_getData(StockItemInterface::IS_IN_STOCK);
    }

    /**
     * Set Stock Availability
     *
     * @param bool|int $isInStock
     * @return $this
     */
    public function setIsInStock($isInStock)
    {
        return $this->setData(StockItemInterface::IS_IN_STOCK, $isInStock);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsQtyDecimal()
    {
        return $this->_getData(StockItemInterface::IS_QTY_DECIMAL);
    }

    /**
     * @param bool $isQtyDecimal
     * @return $this
     */
    public function setIsQtyDecimal($isQtyDecimal)
    {
        return $this->setData(StockItemInterface::IS_QTY_DECIMAL, $isQtyDecimal);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getShowDefaultNotificationMessage()
    {
        return $this->_getData(StockItemInterface::SHOW_DEFAULT_NOTIFICATION_MESSAGE);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigMinQty()
    {
        return $this->_getData(self::USE_CONFIG_MIN_QTY);
    }

    /**
     * @param bool $useConfigMinQty
     * @return $this
     */
    public function setUseConfigMinQty($useConfigMinQty)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_MIN_QTY, $useConfigMinQty);
    }

    /**
     * Retrieve minimal quantity available for item status in stock
     *
     * @return float
     */
    public function getMinQty()
    {
        return $this->_getData(StockItemInterface::MIN_QTY);
    }

    /**
     * Set minimal quantity available for item status in stock
     *
     * @param float $minQty
     * @return $this
     */
    public function setMinQty($minQty)
    {
        return $this->setData(StockItemInterface::MIN_QTY, $minQty);
    }

    /**
     * @return int
     */
    public function getUseConfigMinSaleQty()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_MIN_SALE_QTY);
    }

    /**
     * @param int $useConfigMinSaleQty
     * @return $this
     */
    public function setUseConfigMinSaleQty($useConfigMinSaleQty)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_MIN_SALE_QTY, $useConfigMinSaleQty);
    }

    /**
     * Retrieve Minimum Qty Allowed in Shopping Cart or NULL when there is no limitation
     *
     * @return float
     */
    public function getMinSaleQty()
    {
        return $this->_getData(StockItemInterface::MIN_SALE_QTY);
    }

    /**
     * Set Minimum Qty Allowed in Shopping Cart or NULL when there is no limitation
     *
     * @param float $minSaleQty
     * @return $this
     */
    public function setMinSaleQty($minSaleQty)
    {
        return $this->setData(StockItemInterface::MIN_SALE_QTY, $minSaleQty);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigMaxSaleQty()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_MAX_SALE_QTY);
    }

    /**
     * @param bool $useConfigMaxSaleQty
     * @return $this
     */
    public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_MAX_SALE_QTY, $useConfigMaxSaleQty);
    }

    /**
     * Retrieve Maximum Qty Allowed in Shopping Cart data wrapper
     *
     * @return float
     */
    public function getMaxSaleQty()
    {
        return $this->_getData(StockItemInterface::MAX_SALE_QTY);
    }

    /**
     * Set Maximum Qty Allowed in Shopping Cart data wrapper
     *
     * @param float $maxSaleQty
     * @return $this
     */
    public function setMaxSaleQty($maxSaleQty)
    {
        return $this->setData(StockItemInterface::MAX_SALE_QTY, $maxSaleQty);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigBackorders()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_BACKORDERS);
    }

    /**
     * @param bool $useConfigBackorders
     * @return $this
     */
    public function setUseConfigBackorders($useConfigBackorders)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_BACKORDERS, $useConfigBackorders);
    }

    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {
        return $this->_getData(StockItemInterface::BACKORDERS);
    }

    /**
     * Set backOrders status
     *
     * @param int $backOrders
     * @return $this
     */
    public function setBackorders($backOrders)
    {
        return $this->setData(StockItemInterface::BACKORDERS, $backOrders);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigNotifyStockQty()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_NOTIFY_STOCK_QTY);
    }

    /**
     * @param bool $useConfigNotifyStockQty
     * @return $this
     */
    public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_NOTIFY_STOCK_QTY, $useConfigNotifyStockQty);
    }

    /**
     * Retrieve Notify for Quantity Below data wrapper
     *
     * @return float
     */
    public function getNotifyStockQty()
    {
        return $this->_getData(StockItemInterface::NOTIFY_STOCK_QTY);
    }

    /**
     * Set Notify for Quantity Below data wrapper
     *
     * @param float $notifyStockQty
     * @return $this
     */
    public function setNotifyStockQty($notifyStockQty)
    {
        return $this->setData(StockItemInterface::NOTIFY_STOCK_QTY, $notifyStockQty);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigQtyIncrements()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_QTY_INCREMENTS);
    }

    /**
     * @param bool $useConfigQtyIncrements
     * @return $this
     */
    public function setUseConfigQtyIncrements($useConfigQtyIncrements)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_QTY_INCREMENTS, $useConfigQtyIncrements);
    }

    /**
     * Retrieve Quantity Increments data wrapper
     *
     * @return float|false
     */
    public function getQtyIncrements()
    {
        return $this->_getData(StockItemInterface::QTY_INCREMENTS);
    }

    /**
     * Set Quantity Increments data wrapper
     *
     * @param float $qtyIncrements
     * @return $this
     */
    public function setQtyIncrements($qtyIncrements)
    {
        return $this->setData(StockItemInterface::QTY_INCREMENTS, $qtyIncrements);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigEnableQtyInc()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_ENABLE_QTY_INC);
    }

    /**
     * @param bool $useConfigEnableQtyInc
     * @return $this
     */
    public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_ENABLE_QTY_INC, $useConfigEnableQtyInc);
    }

    /**
     * Retrieve whether Quantity Increments is enabled
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEnableQtyIncrements()
    {
        return $this->_getData(StockItemInterface::ENABLE_QTY_INCREMENTS);
    }

    /**
     * Set whether Quantity Increments is enabled
     *
     * @param bool $enableQtyIncrements
     * @return $this
     */
    public function setEnableQtyIncrements($enableQtyIncrements)
    {
        return $this->setData(StockItemInterface::ENABLE_QTY_INCREMENTS, $enableQtyIncrements);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseConfigManageStock()
    {
        return $this->_getData(StockItemInterface::USE_CONFIG_MANAGE_STOCK);
    }

    /**
     * @param bool $useConfigManageStock
     * @return $this
     */
    public function setUseConfigManageStock($useConfigManageStock)
    {
        return $this->setData(StockItemInterface::USE_CONFIG_MANAGE_STOCK, $useConfigManageStock);
    }

    /**
     * Retrieve can Manage Stock
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getManageStock()
    {
        return $this->_getData(StockItemInterface::MANAGE_STOCK);
    }

    /**
     * @param bool $manageStock
     * @return $this
     */
    public function setManageStock($manageStock)
    {
        return $this->setData(StockItemInterface::MANAGE_STOCK, $manageStock);
    }

    /**
     * @return string
     */
    public function getLowStockDate()
    {
        return $this->_getData(StockItemInterface::LOW_STOCK_DATE);
    }

    /**
     * @param string $lowStockDate
     * @return $this
     */
    public function setLowStockDate($lowStockDate)
    {
        return $this->setData(StockItemInterface::LOW_STOCK_DATE, $lowStockDate);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDecimalDivided()
    {
        return $this->_getData(StockItemInterface::IS_DECIMAL_DIVIDED);
    }

    /**
     * @param bool $isDecimalDivided
     * @return $this
     */
    public function setIsDecimalDivided($isDecimalDivided)
    {
        return $this->setData(StockItemInterface::IS_DECIMAL_DIVIDED, $isDecimalDivided);
    }

    /**
     * @return int
     */
    public function getStockStatusChangedAuto()
    {
        return $this->_getData(StockItemInterface::STOCK_STATUS_CHANGED_AUTO);
    }

    /**
     * @param int $stockStatusChangedAuto
     * @return $this
     */
    public function setStockStatusChangedAuto($stockStatusChangedAuto)
    {
        return $this->setData(StockItemInterface::STOCK_STATUS_CHANGED_AUTO, $stockStatusChangedAuto);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\CatalogInventory\Api\Data\StockItemExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getData(StockItemInterface::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\CatalogInventory\Api\Data\StockItemExtensionInterface $extensionAttributes
    )
    {
        return $this->setData(StockItemInterface::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}