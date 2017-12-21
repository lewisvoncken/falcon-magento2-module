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
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setFirstName($param);

    /**
     * @return string | null
     */
    public function getFirstName();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setGender($param);

    /**
     * @return string | null
     */
    public function getGender();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setInfix($param);

    /**
     * @return string | null
     */
    public function getInfix();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setLastName($param);

    /**
     * @return string | null
     */
    public function getLastName();

    /**
     * @param string $param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setTelephoneNumber($param);

    /**
     * @return string | null
     */
    public function getTelephoneNumber();

    /**
     * @param string$param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthDayOfMonth($param);

    /**
     * @return string | null
     */
    public function getDateOfBirthDayOfMonth();

    /**
     * @param string$param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthMonth($param);

    /**
     * @return string | null
     */
    public function getDateOfBirthMonth();

    /**
     * @param string$param
     * @return \Hatimeria\Reagento\Api\Data\AdyenRedirectShopperInterface
     */
    public function setDateOfBirthYear($param);

    /**
     * @return string | null
     */
    public function getDateOfBirthYear();
}