<?php
namespace Creativestyle\ProductBestsellersRankingExtension\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CalculationMethodsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation
     */
    private $scoreCalculationModel;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider
     */
    private $boostingFactorDataProvider;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->scoreCalculationModel = $objectManager->create(\Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation::class);
        $this->boostingFactorDataProvider = $objectManager->get(\Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider::class);
        $this->productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoDataFixture loadOrders
     */
    public function testCalculation()
    {
        $scoreCalculationModel = $this->scoreCalculationModel;
        $this->boostingFactorDataProvider->setBoostingFactors($this->getBoostingFactorArray());
        $scoreCalculationModel->recalculateScore();

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::INDEXER_ID)->reindexAll();

        $product = $this->productRepository->get('simple-100000');
        $this->assertEquals(6001, $product->getBestsellerScoreByAmount());
        $this->assertEquals(6000001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(301, $product->getBestsellerScoreBySale());

        $product = $this->productRepository->get('simple-400000');
        $this->assertEquals(5101, $product->getBestsellerScoreByAmount());
        $this->assertEquals(20400001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(301, $product->getBestsellerScoreBySale());

        $product = $this->productRepository->get('simple-600000');
        $this->assertEquals(4501, $product->getBestsellerScoreByAmount());
        $this->assertEquals(27000001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(301, $product->getBestsellerScoreBySale());

        $product = $this->productRepository->get('simple-1200000');
        $this->assertEquals(2701, $product->getBestsellerScoreByAmount());
        $this->assertEquals(32400001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(301, $product->getBestsellerScoreBySale());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoDataFixture loadOrders
     */
    public function testCalculationIncludingMultiplier()
    {
        $scoreCalculationModel = $this->scoreCalculationModel;
        $this->boostingFactorDataProvider->setBoostingFactors($this->getBoostingFactorArray());
        $scoreCalculationModel->recalculateScore();

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::INDEXER_ID)->reindexAll();

        $product = $this->productRepository->get('simple-3000000');
        $this->assertEquals(31, $product->getBestsellerScoreByAmount());
        $this->assertEquals(600001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(31, $product->getBestsellerScoreBySale());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoDataFixture loadOrders
     * @magentoConfigFixture current_store bestsellers/sorting/direction desc
     */
    public function testCalculationWithDescendingOrder()
    {
        $scoreCalculationModel = $this->scoreCalculationModel;
        $this->boostingFactorDataProvider->setBoostingFactors($this->getBoostingFactorArray());
        $scoreCalculationModel->recalculateScore();

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::INDEXER_ID)->reindexAll();

        $product = $this->productRepository->get('simple-1200000');
        $this->assertEquals(3301, $product->getBestsellerScoreByAmount());
        $this->assertEquals(600001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(1, $product->getBestsellerScoreBySale());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoDataFixture loadOrders
     * @magentoConfigFixture current_store bestsellers/boosting_factors/boosting_factor_sold_out 0.5
     */
    public function testSoldOutProductCalculation()
    {
        $scoreCalculationModel = $this->scoreCalculationModel;
        $this->boostingFactorDataProvider->setBoostingFactors($this->getBoostingFactorArray());
        $scoreCalculationModel->recalculateScore();

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::INDEXER_ID)->reindexAll();

        $product = $this->productRepository->get('simple-4000000');
        $this->assertEquals(151, $product->getBestsellerScoreByAmount());
        $this->assertEquals(3000001, $product->getBestsellerScoreByTurnover());
        $this->assertEquals(151, $product->getBestsellerScoreBySale());
    }

    public static function loadProducts() {
        include __DIR__.'/../../_files/product_add.php';
    }

    public static function loadOrders() {
        include __DIR__.'/../../_files/order.php';
    }

    protected function getBoostingFactorArray(){
        return [
            'boosterA' =>
                [
                    'value' => 3,
                    'max_days_old' => 7
                ],
            'boosterB' =>
                [
                    'value' => 2,
                    'max_days_old' => 30
                ],
            'boosterC' =>
                [
                    'value' => 1,
                    'max_days_old' => 365
                ],
            'boosterD' =>
                [
                    'value' => 0,
                    'max_days_old' => 999999999
                ]
        ];
    }

    protected function getOrderMapper()
    {
        return [
            2000001 => [
                'price' => 10,
                'product_price' => 10,
                'product_id' => 100000,
                'qty_ordered' => 20
            ],
            2000002 => [
                'price' => 20,
                'product_price' => 20,
                'product_id' => 200000,
                'qty_ordered' => 19
            ],
            2000003 => [
                'price' => 30,
                'product_price' => 30,
                'product_id' => 300000,
                'qty_ordered' => 18
            ],
            2000004 => [
                'price' => 40,
                'product_price' => 40,
                'product_id' => 400000,
                'qty_ordered' => 17
            ],
            2000005 => [
                'price' => 50,
                'product_price' => 50,
                'product_id' => 500000,
                'qty_ordered' => 16
            ],
            2000006 => [
                'price' => 60,
                'product_price' => 60,
                'product_id' => 600000,
                'qty_ordered' => 15
            ],
            2000007 => [
                'price' => 70,
                'product_price' => 70,
                'product_id' => 700000,
                'qty_ordered' => 14
            ],
            2000008 => [
                'price' => 80,
                'product_price' => 80,
                'product_id' => 800000,
                'qty_ordered' => 13
            ],
            2000009 => [
                'price' => 90,
                'product_price' => 90,
                'product_id' => 900000,
                'qty_ordered' => 12
            ],
            20000010 => [
                'price' => 100,
                'product_price' => 100,
                'product_id' => 1000000,
                'qty_ordered' => 11
            ],
            20000011 => [
                'price' => 110,
                'product_price' => 110,
                'product_id' => 1100000,
                'qty_ordered' => 10
            ],
            20000012 => [
                'price' => 120,
                'product_price' => 120,
                'product_id' => 1200000,
                'qty_ordered' => 9
            ],
            20000013 => [
                'price' => 130,
                'product_price' => 130,
                'product_id' => 1300000,
                'qty_ordered' => 8
            ],
            20000014 => [
                'price' => 140,
                'product_price' => 140,
                'product_id' => 1400000,
                'qty_ordered' => 7
            ],
            20000015 => [
                'price' => 150,
                'product_price' => 150,
                'product_id' => 1500000,
                'qty_ordered' => 6
            ],
            20000016 => [
                'price' => 160,
                'product_price' => 160,
                'product_id' => 1600000,
                'qty_ordered' => 5
            ],
            20000017 => [
                'price' => 170,
                'product_price' => 170,
                'product_id' => 1700000,
                'qty_ordered' => 4
            ],
            20000018 => [
                'price' => 180,
                'product_price' => 180,
                'product_id' => 1800000,
                'qty_ordered' => 3
            ],
            20000019 => [
                'price' => 190,
                'product_price' => 190,
                'product_id' => 1900000,
                'qty_ordered' => 2
            ],
            20000020 => [
                'price' => 200,
                'product_price' => 200,
                'product_id' => 2000000,
                'qty_ordered' => 1
            ]
        ];
    }
}