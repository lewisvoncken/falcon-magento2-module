<?php

namespace Hatimeria\Reagento\Api\Data;

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
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUrlPath();

    /**
     * @param string $urlPath
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setUrlPath($urlPath);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return array
     */
    public function getUrlQuery();

    /**
     * @param array $urlQuery
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function setUrlQuery($urlQuery);

    /**
     * @param array $data
     * @return \Hatimeria\Reagento\Api\Data\BreadcrumbInterface
     */
    public function loadFromData($data);

}