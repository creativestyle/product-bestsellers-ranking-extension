<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Repository;

class OrderItemsCollection
{
    protected $filteredCollection = null;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $salesOrderItemsCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $salesOrderItemsCollectionFactory
    )
    {
        $this->salesOrderItemsCollectionFactory = $salesOrderItemsCollectionFactory;
    }

    public function getOrderItemsCollection($dateFrom, $dateTo, $periodFilter)
    {

        $collection = $this->salesOrderItemsCollectionFactory->create()
            ->addFieldToSelect(['sku', 'product_id', 'price', 'qty_ordered']);

        if($dateFrom) {
            $from = new \DateTime($dateFrom);
            $collection
                ->addFieldToFilter('created_at', ['gteq' => $from->format('Y-m-d 00:00:00')]);

        }

        if($dateTo) {
            $to = new \DateTime($dateTo);
            $collection
                ->addFieldToFilter('created_at', ['lteq' => $to->format('Y-m-d 23:59:59')]);
        }

        if (!$periodFilter) {
            return $collection;
        }

        $collection
            ->addFieldToFilter('created_at', ['gteq' => $periodFilter]);

        return $collection;
    }
}