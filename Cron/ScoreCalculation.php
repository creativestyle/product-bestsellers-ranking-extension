<?php
namespace Creativestyle\ProductBestsellersRankingExtension\Cron;

class ScoreCalculation
{
    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation
     */
    protected $scoreCalculation;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\ClearDailyScore
     */
    private $clearDailyScore;
    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\Indexer
     */
    private $indexer;

    /**
     * ScoreCalculation constructor.
     * @param \Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation $scoreCalculation
     * @param \Creativestyle\ProductBestsellersRankingExtension\Model\ClearDailyScore $clearDailyScore
     * @param \Creativestyle\ProductBestsellersRankingExtension\Model\Indexer $indexer
     */
    public function __construct(
        \Creativestyle\ProductBestsellersRankingExtension\Model\ScoreCalculation $scoreCalculation,
        \Creativestyle\ProductBestsellersRankingExtension\Model\ClearDailyScore $clearDailyScore,
        \Creativestyle\ProductBestsellersRankingExtension\Model\Indexer $indexer
    )
    {
        $this->scoreCalculation = $scoreCalculation;
        $this->clearDailyScore = $clearDailyScore;
        $this->indexer = $indexer;
    }

    public function execute()
    {
        $this->clearDailyScore->clearDailyScoring();
        $this->scoreCalculation->recalculateScore();
        $this->indexer->reindex();
    }
}