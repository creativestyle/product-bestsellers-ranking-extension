<?php

namespace Creativestyle\ProductBestsellersRankingExtension\DataProviders;

class OrdersPeriodFilterDataProvider
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getOrdersPeriodFilter()
    {
        $periodConfig = $this->scopeConfig->getValue('bestsellers/orders_period/period');

        switch ($periodConfig) {
            case 0:
                $result = false;
                break;
            case 1:
                $result = date('Y-m-d 00:00:00', strtotime('-7 days'));
                break;
            case 2:
                $result = date('Y-m-d 00:00:00', strtotime('-30 days'));
                break;
            case 3:
                $result = date('Y-m-d 00:00:00', strtotime('-1 year'));
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }
}