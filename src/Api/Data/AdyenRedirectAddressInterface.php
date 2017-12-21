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
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCity($data);

    /**
     * @return string | null
     */
    public function getCity();

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setCountry($data);

    /**
     * @return string | null
     */
    public function getCountry();

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setHouseNumberOrName($data);

    /**
     * @return string | null
     */
    public function getHouseNumberOrName();

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setPostalCode($data);

    /**
     * @return string | null
     */
    public function getPostalCode();

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStateOrProvince($data);

    /**
     * @return string | null
     */
    public function getStateOrProvince();

    /**
     * @param string $data
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectAddressInterface
     */
    public function setStreet($data);

    /**
     * @return string | null
     */
    public function getStreet();
}