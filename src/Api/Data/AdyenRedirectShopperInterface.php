<?php

namespace Hatimeria\Reagento\Api\Data;

interface AdyenRedirectShopperInterface
{
    const FIRST_NAME = 'first_name';
    const GENDER = 'gender';
    const LAST_NAME = 'last_name';
    const TELEPHONE_NUMBER = 'telephone_number';
    const INFIX = 'infix';
    const DATE_OF_BIRTH_DAY_OF_MONTH = 'date_of_birth_day_of_month';
    const DATE_OF_BIRTH_MONTH = 'date_of_birth_month';
    const DATE_OF_BIRTH_YEAR = 'date_of_birth_year';

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setFirstName($value);

    /**
     * @return string | null
     */
    public function getFirstName();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setGender($value);

    /**
     * @return string | null
     */
    public function getGender();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setInfix($value);

    /**
     * @return string | null
     */
    public function getInfix();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setLastName($value);

    /**
     * @return string | null
     */
    public function getLastName();

    /**
     * @param string $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setTelephoneNumber($value);

    /**
     * @return string | null
     */
    public function getTelephoneNumber();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthDayOfMonth($value);

    /**
     * @return integer | null
     */
    public function getDateOfBirthDayOfMonth();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthMonth($value);

    /**
     * @return integer | null
     */
    public function getDateOfBirthMonth();

    /**
     * @param integer $value
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthYear($value);

    /**
     * @return integer | null
     */
    public function getDateOfBirthYear();
}