<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
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

    /**
     * @var \Magento\Eav\Model\Config $eavConfig
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\Config\ScopeInterface $scope
     */
    protected $scope;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action $action
     */
    protected $productResourceAction;

    /**
     * @var \Magento\Store\Model\StoreManager $storeManager
     */
    protected $storeManager;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetupInterface,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Magento\Store\Model\StoreManager $storeManager
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);
        $this->eavConfig = $eavConfig;
        $this->state = $state;
        $this->scope = $scope;
        $this->productFactory = $productFactory;
        $this->productResourceAction = $productResourceAction;
        $this->storeManager = $storeManager;
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_by_amount',
                'default_value',
                1
            );

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_by_turnover',
                'default_value',
                1
            );
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_multiplier',
                [
                    'label' => 'Bestseller Score Multiplier',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'Bestsellers',
                    'type' => 'int',
                    'input' => 'text',
                    'frontend' => '',
                    'visible' => 1,
                    'required' => 0,
                    'user_defined' => 1,
                    'used_for_price_rules' => 1,
                    'position' => 2,
                    'unique' => 0,
                    'default' => 100,
                    'sort_order' => 400,
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
                    'system' => 0,
                    'note' => 'Value in %'
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_by_sale',
                [
                    'label' => 'Bestseller Score By Sale',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'Bestsellers',
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
                    'sort_order' => 300,
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
                    'system' => 0,
                    'note' => 'Field will be overwritten automatically every night'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_multiplier',
                [
                    'used_for_price_rules' => 0,
                    'is_searchable' => 0,
                    'is_visible_in_advanced_search' => 0,
                    'is_comparable' => 0,
                    'is_filterable' => 0,
                    'is_filterable_in_search' => 0,
                    'is_used_for_promo_rules' => 0,
                    'is_html_allowed_on_front' => 0,
                    'is_visible_on_front' => 0,
                    'used_in_product_listing' => 0,
                ],
                1
            );

            try {
                $this->state->setAreaCode('frontend');
            } catch (\Exception $e) {}

            $ids = $this->productFactory->create()->getProductEntitiesInfo(['entity_id']);

            $ids = array_column($ids, 'entity_id');

            $storeId = $this->storeManager->getStore()->getId();

            if (!empty($ids)) {
                $this->productResourceAction->updateAttributes([$ids], ['bestseller_score_multiplier' => 100], $storeId);
            }
        }

        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'bestseller_score_multiplier',
                [
                    'used_for_sort_by' => 0
                ],
                1
            );
        }

        $setup->endSetup();
    }
}
