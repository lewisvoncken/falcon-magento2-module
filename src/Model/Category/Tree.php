<?php

namespace Deity\MagentoApi\Model\Category;

use Deity\MagentoApi\Api\Data\CategoryTreeInterface;

class Tree extends \Magento\Catalog\Model\Category\Tree
{
    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param null $depth
     * @param int $currentLevel
     * @return CategoryTreeInterface
     */
    public function getTree($node, $depth = null, $currentLevel = 0)
    {
        /** @var CategoryTreeInterface $tree */
        $tree = parent::getTree($node, $depth, $currentLevel);
        $tree->setUrlPath($node->getData('url_path'));
        return $tree;
    }
}