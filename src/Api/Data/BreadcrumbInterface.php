<?php

namespace Deity\MagentoApi\Api\Data;

interface BreadcrumbInterface
{
    const ID = 'id';
    const NAME = 'name';
    const URL_PATH = 'url_path';
    const URL_KEY = 'url_key';
    const URL_QUERY = 'url_query';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUrlPath();

    /**
     * @param string $urlPath
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlPath($urlPath);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return array
     */
    public function getUrlQuery();

    /**
     * @param array $urlQuery
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function setUrlQuery($urlQuery);

    /**
     * @param array $data
     * @return \Deity\MagentoApi\Api\Data\BreadcrumbInterface
     */
    public function loadFromData($data);

}