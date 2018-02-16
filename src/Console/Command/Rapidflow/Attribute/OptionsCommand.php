<?php

namespace Deity\MagentoApi\Console\Command\Rapidflow\Attribute;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\User\Model\ResourceModel\User as AdminUser;
use Magento\Store\Model\StoreManagerInterface;

class OptionsCommand extends Command
{
    const OPTION_SHOP = 'shop';

    const OPTION_ATTRIBUTE_CODE = 'attribute_codes';

    protected $attributeCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var string
     */
    protected $exportPath;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $_directorylist,
        StoreManagerInterface $storeManager,
        $name = null
    ) {
        $this->attributeCollectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->exportPath = $_directorylist->getPath('var') . '/urapidflow/'  . 'attribute-options.csv';

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('entity_type_id', 4)
            ->addFieldToFilter('frontend_input', ['select', 'multiselect']);

        // Attribute code filtering 
        if ($attributeCodes = $input->getOption(self::OPTION_ATTRIBUTE_CODE)) {
            $attributeCodes = explode(',', $attributeCodes);

            $collection->addFieldToFilter('attribute_code', $attributeCodes);
        }

        // store generation, single from option or all
        $stores = [];
        if ($storeCode = $input->getOption(self::OPTION_SHOP)) {
            $store = $this->_storeManager->getStore($storeCode);
            $stores[$store->getId()] = $store->getCode();
        } else {
            foreach ($this->_storeManager->getStores(false) as $store) {
                $stores[$store->getId()] = $store->getCode();
            }
        }

        // open export file and overwrite with new data
        $fh = fopen($this->exportPath, 'w');
        foreach ($collection as $attribute) {
            foreach ($stores as $storeId => $storeCode) {
                $source = $attribute->getSource();

                $defaultLabels = [];
                foreach ($source->getAllOptions(false, true) as $option) {
                    $defaultLabels[$option['value']] = $option['label'];
                }

                foreach ($source->getAllOptions(false) as $option) {
                    $row = [
                        'EAOL',
                        $attribute->getAttributeCode(),
                        $defaultLabels[$option['value']],
                        $storeCode,
                        $option['label'],
                    ];
                    fputcsv($fh, $row);
                }
            }
        }
        fclose($fh);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rapidflow:attribute:options');
        $this->setDescription('Generate import file for option label translation');
        $this->addOption(
            self::OPTION_SHOP,
            's',
            InputOption::VALUE_REQUIRED,
            ''
        );
        $this->addOption(
            self::OPTION_ATTRIBUTE_CODE,
            null,
            InputOption::VALUE_REQUIRED,
            ''
        );
        parent::configure();
    }
}

