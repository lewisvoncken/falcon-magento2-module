<?php

namespace Deity\MagentoApi\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * OrderIdMask model
 *
 * @method string getMaskedId()
 * @method OrderIdMask setMaskedId()
 * @method int getOrderId()
 * @method OrderIdMask setOrderId()
 */
class OrderIdMask extends AbstractModel
{
    /**
     * @var Random
     */
    protected $randomDataGenerator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Random $randomDataGenerator
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Random $randomDataGenerator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->randomDataGenerator = $randomDataGenerator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Deity\MagentoApi\Model\ResourceModel\Order\OrderIdMask');
    }

    /**
     * Initialize quote identifier before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if (!$this->getData('masked_id')) {
            $this->setData('masked_id', $this->randomDataGenerator->getUniqueHash());
        }
        return $this;
    }
}
