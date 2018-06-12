<?php

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize();

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement */

require __DIR__ . '/../_files/product_mapper.php';

foreach ($productMapper as $id => $data) {
    $categoryLinkManagement = $objectManager->create('Magento\Catalog\Api\CategoryLinkManagementInterface');
    /** @var $product \Magento\Catalog\Model\Product */
    $product = $objectManager->create('Magento\Catalog\Model\Product');
    $product->isObjectNew(true);
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setId($id)
        ->setAttributeSetId(4)
        ->setWebsiteIds([1])
        ->setName('Simple Product' . $id)
        ->setSku('simple-'. $id)
        ->setPrice($data['price'])
        ->setWeight(1)
        ->setShortDescription("Short description")
        ->setTaxClassId(0)
        ->setTierPrice(
            [
                [
                    'website_id' => 0,
                    'cust_group' => \Magento\Customer\Model\Group::CUST_GROUP_ALL,
                    'price_qty' => 2,
                    'price' => 8,
                ],
                [
                    'website_id' => 0,
                    'cust_group' => \Magento\Customer\Model\Group::CUST_GROUP_ALL,
                    'price_qty' => 5,
                    'price' => 5,
                ],
                [
                    'website_id' => 0,
                    'cust_group' => \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID,
                    'price_qty' => 3,
                    'price' => 5,
                ],
            ]
        )
        ->setDescription('Description with <b>html tag</b>')
        ->setMetaTitle('meta title')
        ->setMetaKeyword('meta keyword')
        ->setMetaDescription('meta description')
        ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => isset($data['qty']) ? $data['qty'] : 100,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1,
            ]
        )->setCanSaveCustomOptions(false)
        ->setBestsellerScoreByAmount(0)
        ->setBestsellerScoreByTurnover(0)
        ->setBestsellerScoreMultiplier(isset($data['multiplier']) ? $data['multiplier'] : 100)
        ->setQty(isset($data['qty']) ? $data['qty'] : 100)
        ->setHasOptions(false);
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryFactory */
    $productRepositoryFactory = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
    $productRepositoryFactory->save($product);
}
