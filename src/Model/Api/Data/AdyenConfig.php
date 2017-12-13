<?php
namespace Hatimeria\Reagento\Model\Api\Data;

use Hatimeria\Reagento\Api\Data\AdyenConfigInterface;
use Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class AdyenConfig extends AbstractExtensibleModel implements AdyenConfigInterface
{
    /**
     * @param string $key
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCsePublicKey($key)
    {
        return $this->setData(AdyenConfigInterface::CSE_PUBLIC_KEY, $key);
    }

    /**
     * @return string
     */
    public function getCsePublicKey()
    {
        return $this->_getData(AdyenConfigInterface::CSE_PUBLIC_KEY);
    }

    /**
     * @param bool $flag
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCcEnabled($flag)
    {
        return $this->setData(AdyenConfigInterface::CC_ENABLED, $flag);
    }

    /**
     * @return bool
     */
    public function getCcEnabled()
    {
        return $this->_getData(AdyenConfigInterface::CC_ENABLED);
    }

    /**
     * @param string[] $cards
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCcAvailableCards($cards)
    {
        if (!is_array($cards)) {
            $cards = explode(',', $cards);
        }
        return $this->setData(AdyenConfigInterface::CC_AVAILABLE_CARDS, $cards);
    }

    /**
     * @return string[]
     */
    public function getCcAvailableCards()
    {
        return $this->_getData(AdyenConfigInterface::CC_AVAILABLE_CARDS);
    }

    /**
     * @param string $imageUrl
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectInterface
     */
    public function setCcImage($imageUrl)
    {
        return $this->setData(AdyenConfigInterface::CC_IMAGE, $imageUrl);
    }

    /**
     * @return string
     */
    public function getCcImage()
    {
        return $this->_getData(AdyenConfigInterface::CC_IMAGE);
    }

    /**
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            /** @var AdyenConfigExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionAttributesFactory->create(AdyenConfigInterface::class);
        }

        return $extensionAttributes;
    }

    /**
     * @param \Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface $extensionAttributes
     * @return AdyenConfigInterface
     */
    public function setExtensionAttributes(AdyenConfigExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}