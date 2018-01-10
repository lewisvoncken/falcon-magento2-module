<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectAddressInterface
{
    const CITY = 'city';
    const COUNTRY = 'country';
    const HOUSE_NUMBER_OR_NAME = 'house_number_or_name';
    const POSTAL_CODE = 'postal_code';
    const STATE_OR_PROVINCE = 'state_or_province';
    const STREET = 'street';

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCity($value);

    /**
     * @return string | null
     */
    public function getCity();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCountry($value);

    /**
     * @return string | null
     */
    public function getCountry();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setHouseNumberOrName($value);

    /**
     * @return string | null
     */
    public function getHouseNumberOrName();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setPostalCode($value);

    /**
     * @return string | null
     */
    public function getPostalCode();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStateOrProvince($value);

    /**
     * @return string | null
     */
    public function getStateOrProvince();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStreet($value);

    /**
     * @return string | null
     */
    public function getStreet();
}