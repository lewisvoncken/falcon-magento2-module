<?php

namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\BreadcrumbInterface;
use Hatimeria\Reagento\Api\Data\BreadcrumbExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Breadcrumb extends AbstractExtensibleModel implements BreadcrumbInterface
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param int $id
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
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
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
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
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
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
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
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
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setUrlQuery($urlQuery)
    {
        return $this->setData(self::URL_QUERY, $urlQuery);
    }

    /**
     * @param array $data
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function loadFromData($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                $this->_logger->debug(__("Unknown field '{$key}' in BreadcrumbsInterface"));
            }
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