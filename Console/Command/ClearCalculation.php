<?php
namespace Creativestyle\ProductBestsellersRankingExtension\Console\Command;

class ClearCalculation extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Creativestyle\ProductBestsellersRankingExtension\Model\ClearDailyScoreFactory
     */
    protected $clearDailyScoringFactory;

    /**
     * @var \Magento\Framework\Config\ScopeInterface $scope
     */
    private $scope;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Creativestyle\ProductBestsellersRankingExtension\Model\ClearDailyScoreFactory $clearDailyScoringFactory
    )
    {
        parent::__construct();

        $this->state = $state;
        $this->clearDailyScoringFactory = $clearDailyScoringFactory;
        $this->scope = $scope;
    }

    protected function configure()
    {

        $this->setName('cs:bestsellers:clear');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        if ($this->scope->getCurrentScope() !== 'frontend') {
            $this->state->setAreaCode('frontend');
        }

        $clearDailyScoring = $this->clearDailyScoringFactory->create();

        $clearDailyScoring->clearDailyScoring();
    }
}