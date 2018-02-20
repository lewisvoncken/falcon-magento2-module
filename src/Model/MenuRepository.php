<?php

namespace Deity\MagentoApi\Model;

use Deity\MagentoApi\Api\Data\MenuInterface;
use Deity\MagentoApi\Api\Data\MenuInterfaceFactory;
use Deity\MagentoApi\Api\MenuRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Topmenu;
use Psr\Log\LoggerInterface;

class MenuRepository implements MenuRepositoryInterface
{
    /** @var MenuInterfaceFactory */
    protected $menuFactory;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var ManagerInterface */
    protected $eventManager;

    /** @var BlockFactory */
    protected $blockFactory;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * MenuRepository constructor.
     * @param MenuInterfaceFactory $menuFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ManagerInterface $eventManager
     * @param BlockFactory $blockFactory yes, really, check further description below
     * @param LoggerInterface $logger
     */
    public function __construct(
        MenuInterfaceFactory $menuFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $eventManager,
        BlockFactory $blockFactory,
        LoggerInterface $logger
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->blockFactory = $blockFactory;
        $this->menuFactory = $menuFactory;
    }

    /**
     * @return \Deity\MagentoApi\Api\Data\MenuInterface[]
     */
    public function getTree()
    {
        /** @var Node $menuTree */
        $menuTree = $this->getMenuFromTopmenuBlock();

        /** @var MenuInterface[] $items */
        $items = $this->convertMenuNodesToMenuItems($menuTree);

        return $items;
    }

    /**
     * Convert node tree from topmenu block into array of MenuInterface objects
     *
     * @param Node $node
     * @return MenuInterface[]
     */
    protected function convertMenuNodesToMenuItems(Node $node)
    {
        $items = [];
        foreach($node->getChildren() as $childNode) { /** @var Node $childNode */
            $menuItem = $this->menuFactory->create();
            $menuItem->setName($childNode->getName());
            $menuItem->setId($childNode->getId());
            $menuItem->setUrl($childNode->getUrl());
            $menuItem->setLevel($childNode->getLevel());
            $menuItem->setIsActive($childNode->getIsActive());
            $menuItem->setHasActive($childNode->getHasActive());
            $menuItem->setIsFirst($childNode->getIsFirst());
            $menuItem->setIsLast($childNode->getIsLast());
            $menuItem->setPositionClass($childNode->getPositionClass());
            if ($childNode->hasChildren()) {
                $children = $this->convertMenuNodesToMenuItems($childNode);
                $menuItem->setChildren($children);
            }
            $items[] = $menuItem;
        }

        return  $items;
    }

    /**
     * Let me tell you a story of a not so young developer. He was making his way in the magento world. He had some
     * success along the way, getting his certificate, proving himself in various projects. He strove to produce
     * best quality code possible. His recipe was simple, follow best practices rule, make sensible abstraction,
     * logic put in easily reusable code that is independent as most as possible from the context.
     * Do you think person like that produced this method? Well no, it must have been some beginner programmer
     * who does not know magento well enough yet or does not care about best practices rules.
     * Let me disabuse you. All this is done by someone who want to be such person.
     * So what is standing in the way?
     * In short Magento. Ah, you would like to have a little bit more? Ok, let me entertain you.
     * This wonderful piece of code is here because magento2 does not put logic of creating main menu into
     * some reusable class or even event we could call here and get menu data.
     * All is done in beforeGetHtml plugin of Topmenu block.
     * Probably because you want to know what's the menu structure there and you do not care of other developers
     * who will need to deal with your code.
     */
    protected function getMenuFromTopmenuBlock()
    {
        /** @var Topmenu $topMenuBlock */
        $topMenuBlock = $this->blockFactory->createBlock(Topmenu::class);
        //need to call this for plugins to work but we don't care about the generated html,
        // it's just few ms of the processor time we need to waste
        $topMenuBlock->getHtml();

        //now we how menu tree to work with
        return $topMenuBlock->getMenu();
    }
}