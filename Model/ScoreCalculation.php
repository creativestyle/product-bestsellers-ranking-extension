<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Model;

class ScoreCalculation
{
    private $orderStatuses;

    private $boostingFactors;

    private $storeId;

    private $ordersPeriodFilter;

    private $currentOrderCreatedAt;

    protected $productsScoreArray = [];

    protected $dataArray = [];

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $productResourceAction;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider
     */
    protected $boostingFactorDataProvider;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\OrdersPeriodFilterDataProvider
     */
    protected $periodFilter;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\MultiplierDataProvider
     */
    protected $multiplierDataProvider;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Repository\OrderItemsCollection
     */
    protected $ordersItemsCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var integer
     */
    protected $sortOrder;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    protected $eavAttribute;

    /**
     * @var array
     */
    protected $maxScores = [];

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider $boostingFactorDataProvider,
        \Creativestyle\ProductBestsellersRankingExtension\DataProviders\OrdersPeriodFilterDataProvider $ordersPeriodFilterDataProvider,
        \Creativestyle\ProductBestsellersRankingExtension\DataProviders\MultiplierDataProvider $multiplierDataProvider,
        \Creativestyle\ProductBestsellersRankingExtension\Repository\OrderItemsCollection $ordersItemsCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productModel = $productModel;
        $this->productResource = $productResource;
        $this->productResourceAction = $productResourceAction;
        $this->boostingFactorDataProvider = $boostingFactorDataProvider;
        $this->periodFilter = $ordersPeriodFilterDataProvider;
        $this->multiplierDataProvider = $multiplierDataProvider;
        $this->ordersItemsCollection = $ordersItemsCollection;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->eavAttribute = $eavAttribute;
        $this->maxScores = [
            'bestseller_score_by_amount' => 0,
            'bestseller_score_by_turnover' => 0,
            'bestseller_score_by_sale' => 0
        ];
    }

    protected function applyParameters()
    {
        $this->boostingFactors = $this->boostingFactorDataProvider->getBoostingFactors();
        $this->storeId = $this->storeManager->getStore()->getId();
        $this->ordersPeriodFilter = $this->periodFilter->getOrdersPeriodFilter();
        $this->sortOrder = $this->scopeConfig->getValue('bestsellers/sorting/direction', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function recalculateScore()
    {
        $this->applyParameters();
        $this->calculateProductRating();

    }
    public function calculateProductRating()
    {
        $productsCollection = $this->productCollectionFactory->create();
        $productsCollection->addAttributeToSelect(['price', 'bestseller_score_multiplier']);
        $soldOutFactor = floatval($this->scopeConfig->getValue('bestsellers/boosting_factors/boosting_factor_sold_out', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

        /**
         * @var \Dmatthew\AttributeDescription\Model\Entity\Attribute\Interceptor $attribute
         * @var \Magento\Catalog\Model\Product\Interceptor $product
         */
        foreach ($productsCollection as $product) {
            $multiplier = $product->getBestsellerScoreMultiplier();
            $multiplier = $multiplier === null ? 100 : $multiplier;
            $price = $product->getPrice();
            $productId = $product->getId();
            $this->buildSelectForProduct($productId, $price, $multiplier, $soldOutFactor);
        }

        if ($this->sortOrder == 'desc') {
            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('catalog_product_entity_int');
            $amountScoreAttributeId = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_amount');
            $turnoverScoreAttributeId = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_turnover');
            $saleScoreAttributeId = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_sale');

            $sql = $connection->update($table, [
                'value' => new \Zend_Db_Expr($this->maxScores['bestseller_score_by_amount'] + 1 . ' - value'),
            ], ['attribute_id = ?' => $amountScoreAttributeId]);

            $sql = $connection->update($table, [
                'value' => new \Zend_Db_Expr($this->maxScores['bestseller_score_by_turnover'] + 1 . ' - value'),
            ], ['attribute_id = ?' => $turnoverScoreAttributeId]);

            $sql = $connection->update($table, [
                'value' => new \Zend_Db_Expr($this->maxScores['bestseller_score_by_sale'] + 1 . ' - value'),
            ], ['attribute_id = ?' => $saleScoreAttributeId]);
        }
    }

    public function buildSelectForProduct($productId, $price, $multiplier, $soldOutFactor)
    {
        $this->buildQueryByPeriodBooster($productId, $price, $multiplier, $soldOutFactor);
    }

    public function getBaseQuery($productId)
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order_item');
        $stockTableName = $resource->getTableName('cataloginventory_stock_item');

        $sql = $connection
            ->select()
            ->from($tableName, [
                'item_id',
                'qty_ordered',
                'product_id',
                'created_at'
            ])
            ->where($tableName . '.product_id = ?', $productId)
            ->join($stockTableName, $stockTableName . '.product_id = ' . $productId, $stockTableName . '.qty');

        if($this->periodFilter->getOrdersPeriodFilter()) {
            $sql->where("created_at >= '".$this->periodFilter->getOrdersPeriodFilter()."'");
        }

        return $sql;
    }

    public function buildQueryByPeriodBooster($productId, $price, $multiplier, $soldOutFactor)
    {
        $days = 0;

        $this->productResourceAction->updateAttributes(
            [$productId],
            [
                'bestseller_score_by_amount' => 1,
                'bestseller_score_by_turnover' => 1,
                'bestseller_score_by_sale' => 1
            ],
            $this->storeId
        );

        foreach($this->boostingFactorDataProvider->getBoostingFactors() as $period) {
            $periodSql = $this->getBaseQuery($productId);
            $from = date('Y-m-d 00:00:00', strtotime('-'.$period['max_days_old'].' days'));
            $to = date('Y-m-d 23:59:59', strtotime('-'.$days.' days'));

            $days = $period['max_days_old'];

            $periodSql->where("created_at >= '".$from."'");
            $periodSql->where("created_at <= '".$to."'");
            $periodSql->columns('SUM(`sales_order_item`.qty_ordered) AS sum_qty_ordered');
            $periodSql->columns('COUNT(`sales_order_item`.product_id) AS count_ordered')
                ->group('product_id');

            $resource = $this->resourceConnection;
            $connection = $resource->getConnection();
            $result = $connection->fetchRow($periodSql);

            if($result) {
                $amountScore = $this->productResource->getAttributeRawValue($result['product_id'], 'bestseller_score_by_amount', $this->storeId);
                $turnoverScore = $this->productResource->getAttributeRawValue($result['product_id'], 'bestseller_score_by_turnover', $this->storeId);
                $salesScore = $this->productResource->getAttributeRawValue($result['product_id'], 'bestseller_score_by_sale', $this->storeId);

                $qtyMultiplier = 1;
                if (isset($result['qty']) && floatval($result['qty']) == 0) {
                    $qtyMultiplier = $soldOutFactor;
                }

                if(!$amountScore){
                    $amountScore = 1;
                }

                if(!$turnoverScore){
                    $turnoverScore = 1;
                }

                if(!$salesScore){
                    $salesScore = 1;
                }

                $updatedAmountScore = $amountScore + round($result['sum_qty_ordered'] * $period['value'] * $multiplier * $qtyMultiplier);
                $updatedTurnoverScore = $turnoverScore + round($result['sum_qty_ordered'] * $price * $period['value'] * 100 * $multiplier * $qtyMultiplier);
                $updatedSalesScore = $salesScore + round($result['count_ordered'] * $period['value'] * $multiplier * $qtyMultiplier);

                if ($this->maxScores['bestseller_score_by_amount'] < $updatedAmountScore) {
                    $this->maxScores['bestseller_score_by_amount'] = $updatedAmountScore;
                }

                if ($this->maxScores['bestseller_score_by_turnover'] < $updatedTurnoverScore) {
                    $this->maxScores['bestseller_score_by_turnover'] = $updatedTurnoverScore;
                }

                if ($this->maxScores['bestseller_score_by_sale'] < $updatedSalesScore) {
                    $this->maxScores['bestseller_score_by_sale'] = $updatedSalesScore;
                }

                $this->productResourceAction->updateAttributes(
                    [$result['product_id']],
                    [
                        'bestseller_score_by_amount' => $updatedAmountScore,
                        'bestseller_score_by_turnover' => $updatedTurnoverScore,
                        'bestseller_score_by_sale' => $updatedSalesScore
                    ],
                    $this->storeId
                );
            }
        }
    }
}