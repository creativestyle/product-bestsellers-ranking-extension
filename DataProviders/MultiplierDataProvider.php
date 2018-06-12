<?php

namespace Creativestyle\ProductBestsellersRankingExtension\DataProviders;

class MultiplierDataProvider
{

    public function getMultiplier($orderCreatedAt, $boostingFactors)
    {
        $now = new \DateTime();
        $orderCreationDate = new \DateTime($orderCreatedAt);
        $orderOld = $now->diff($orderCreationDate)->format("%a");
        foreach ($boostingFactors as $booster) {
            if ($orderOld <= $booster['max_days_old']) {
                return $booster['value'];
            }
        }

        return 1;
    }
}