<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $setup->getConnection()->addIndex(
                $setup->getTable('sales_order_item'),
                $setup->getIdxName('sales_order_item', ['product_id'], 'index'),
                ['product_id']
            );
        }
        $setup->endSetup();
    }
}
