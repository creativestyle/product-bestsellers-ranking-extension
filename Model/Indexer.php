<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Model;

class Indexer
{
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    )
    {
        $this->indexerFactory = $indexerFactory;
    }

    public function reindex()
    {
        $indexerIds = ['catalog_category_product', 'catalog_product_attribute', 'catalogsearch_fulltext'];
        foreach ($indexerIds as $indexerId) {
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    }

}
