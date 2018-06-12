<?php
namespace Creativestyle\ProductBestsellersRankingExtension\Test\Unit\Model;

/**
 * Class CalculationMethodsTest
 * @package Creativestyle\ProductBestsellersRankingExtension\Test\Unit\Model
 */
class CalculationMethodsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider
     */
    private $boostingFactorDataProvider;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation
     */
    private $scoreCalculationModel;

    private $orderItemModel;

    private $productRepository;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\DataProviders\MultiplierDataProvider
     */
    private $multiplierDataProvider;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->boostingFactorDataProvider = $this->objectManager->get(\Creativestyle\ProductBestsellersRankingExtension\DataProviders\BoostingFactorDataProvider::class);
        $this->scoreCalculationModel = $this->objectManager->create(\Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation::class);
        $this->orderItemModel = $this->objectManager->get(\Magento\Sales\Model\Order\Item::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
        $this->multiplierDataProvider = $this->objectManager->get(\Creativestyle\ProductBestsellersRankingExtension\DataProviders\MultiplierDataProvider::class);
    }

    public function testItReturnsCorrectBoostingFactors()
    {
        $factorDataArray = $this->boostingFactorDataProvider->getBoostingFactors();
        $this->assertEquals($factorDataArray, $this->getCorrectBoostingFactorArray());
    }

    public function testItReturnsCorrectMultiplier()
    {
        $boostingDataProvider = $this->boostingFactorDataProvider->getBoostingFactors();

        $dateMinus5days = date('Y-m-d', strtotime("-5 days"));
        $multiplier = $this->multiplierDataProvider->getMultiplier($dateMinus5days, $boostingDataProvider);

        $this->assertEquals(3, $multiplier);

        $dateMinus10days = date('Y-m-d', strtotime("-10 days"));
        $multiplier = $this->multiplierDataProvider->getMultiplier($dateMinus10days, $boostingDataProvider);

        $this->assertEquals(2, $multiplier);
        $dateMinus90days = date('Y-m-d', strtotime("-90 days"));
        $multiplier = $this->multiplierDataProvider->getMultiplier($dateMinus90days, $boostingDataProvider);

        $this->assertEquals(1, $multiplier);

        $dateMinus120days = date('Y-m-d', strtotime("-120 days"));
        $multiplier = $this->multiplierDataProvider->getMultiplier($dateMinus120days, $boostingDataProvider);

        $this->assertEquals(1, $multiplier);

    }


    protected function getOrderItemsDataArray()
    {
        return [
            '230' => [
                'entity_id' => 230,
                'item_id' => 230,
                'product_id' => 230,
                'qty_ordered' => 5,
                'price' => 69,
                'sku' => 'MH12-XS-Red'
            ],
            '400' => [
                'entity_id' => 400,
                'item_id' => 400,
                'product_id' => 400,
                'qty_ordered' => 7,
                'price' => 56.99,
                'sku' => 'MJ06-XL-Blue'
            ],
            '500' => [
                'entity_id' => 500,
                'item_id' => 500,
                'product_id' => 500,
                'qty_ordered' => 10,
                'price' => 29,
                'sku' => 'MS12-XS-Black'
            ],
            '600' => [
                'entity_id' => 600,
                'item_id' => 600,
                'product_id' => 600,
                'qty_ordered' => 20,
                'price' => 39,
                'sku' => 'MS07-S-Green'
            ],
        ];
    }
    protected function getCorrectBoostingFactorArray(){
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

    protected function getWrongBoostingFactorArray(){
        return [
            'boosterA' =>
                [
                    'value' => null,
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
}