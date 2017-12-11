<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenConfigInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CSE_PUBLIC_KEY = 'cse_public_key';
    const CC_ENABLED = 'cc_enabled';
    const CC_AVAILABLE_CARDS = 'cc_available_cards';
    const CC_IMAGE = 'cc_image';

    /**
     * @return string
     */
    public function getCsePublicKey();

    /**
     * @param string $key
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigInterface
     */
    public function setCsePublicKey($key);

    /**
     * @return bool
     */
    public function getCcEnabled();

    /**
     * @param bool $flag
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigInterface
     */
    public function setCcEnabled($flag);

    /**
     * @return string[]
     */
    public function getCcAvailableCards();

    /**
     * @param string[] $cards
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigInterface
     */
    public function setCcAvailableCards($cards);

    /**
     * @return string
     */
    public function getCcImage();

    /**
     * @param string $imageUrl
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigInterface
     */
    public function setCcImage($imageUrl);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Hatimeria\Reagento\Api\Data\AdyenConfigExtensionInterface $extensionAttributes
    );
}