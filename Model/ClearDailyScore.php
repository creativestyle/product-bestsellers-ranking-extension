<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Model;

class ClearDailyScore
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;


    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->eavAttribute = $eavAttribute;
    }

    public function clearDailyScoring()
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('catalog_product_entity_int');
        $bestsellerScoreByAmountAttribute = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_amount');
        $bestsellerScoreByTurnoverAttribute = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_turnover');
        $bestsellerScoreBySalesAttribute = $this->eavAttribute->getIdByCode('catalog_product', 'bestseller_score_by_sale');

        $field = 'attribute_id';

        $where = $connection->quoteInto($field . ' IN(?) ', [$bestsellerScoreByAmountAttribute, $bestsellerScoreByTurnoverAttribute, $bestsellerScoreBySalesAttribute]);

        $connection->update($tableName, ['value' => 1], $where);
    }
}