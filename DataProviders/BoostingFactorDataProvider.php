<?php

namespace Creativestyle\ProductBestsellersRankingExtension\DataProviders;

class BoostingFactorDataProvider
{
    protected $boostingFactorsConfig = [];

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function setBoostingFactors($boostingFactorsArray)
    {
        $this->boostingFactorsConfig = $boostingFactorsArray;

        return $this;
    }

    public function getBoostingFactors()
    {
        if (!empty($this->boostingFactorsConfig)) {
            return $this->boostingFactorsConfig;
        }

        $scopeConfig = $this->scopeConfig;

        return [
            'boosterA' =>
                [
                    'value' => $scopeConfig->getValue('bestsellers/boosting_factors/boosting_factor_week'),
                    'max_days_old' => 7
                ],
            'boosterB' =>
                [
                    'value' => $scopeConfig->getValue('bestsellers/boosting_factors/boosting_factor_month'),
                    'max_days_old' => 30
                ],
            'boosterC' =>
                [
                    'value' => $scopeConfig->getValue('bestsellers/boosting_factors/boosting_factor_year'),
                    'max_days_old' => 365
                ],
            'boosterD' =>
                [
                    'value' => $scopeConfig->getValue('bestsellers/boosting_factors/boosting_factor_general'),
                    'max_days_old' => 999999999
                ]
        ];
    }
}