<?php

namespace Deity\MagentoApi\Model\Api\Data;

use Deity\MagentoApi\Api\Data\BreadcrumbInterface;
use Deity\MagentoApi\Api\Data\BreadcrumbExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Breadcrumb extends AbstractExtensibleModel implements BreadcrumbInterface
{
    /**
     * List of available fields for loadFromData method
     *
     * @var array
     */
    protected $mappedFields = [
        self::ID => self::ID,
        self::NAME => self::NAME,
        self::URL_KEY => self::URL_KEY,
        self::URL_PATH => self::URL_PATH,
        self::URL_QUERY => self::URL_QUERY
    ];


    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param int $id
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string
     */
    public function getUrlPath()
    {
        return $this->getData(self::URL_PATH);
    }

    /**
     * @param string $urlPath
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlPath($urlPath)
    {
        return $this->setData(self::URL_PATH, $urlPath);
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @param string $urlKey
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * @return array
     */
    public function getUrlQuery()
    {
        return $this->getData(self::URL_QUERY);
    }

    /**
     * @param array $urlQuery
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlQuery($urlQuery)
    {
        return $this->setData(self::URL_QUERY, $urlQuery);
    }

    /**
     * @param array $data
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function loadFromData($data)
    {
        $data = array_intersect_key($data, $this->mappedFields);
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }

        return $this;
    }

    /**
     * @return BreadcrumbExtensionInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes() ?: $this->extensionAttributesFactory->create(BreadcrumbExtensionInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(BreadcrumbExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}