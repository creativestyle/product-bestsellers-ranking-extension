<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetupInterface;

    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetupInterface
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);
    }

    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bestseller_score_by_amount',
            [
                'label' => 'Bestseller Score By Amount',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'type' => 'int',
                'input' => 'text',
                'frontend' => '',
                'visible' => 1,
                'required' => 0,
                'user_defined' => 1,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'default' => '',
                'sort_order' => 200,
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 1,
                'is_visible_in_advanced_search' => 1,
                'is_comparable' => 1,
                'is_filterable' => 1,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 1,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 1,
                'system' => 0

            ]
        );
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'bestseller_score_by_turnover',
            [
                'label' => 'Bestseller Score By Turnover',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'type' => 'int',
                'input' => 'text',
                'frontend' => '',
                'visible' => 1,
                'required' => 0,
                'user_defined' => 1,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'default' => '',
                'sort_order' => 200,
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 1,
                'is_visible_in_advanced_search' => 1,
                'is_comparable' => 1,
                'is_filterable' => 1,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 1,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 1,
                'system' => 0

            ]
        );

        $setup->endSetup();
    }
}
