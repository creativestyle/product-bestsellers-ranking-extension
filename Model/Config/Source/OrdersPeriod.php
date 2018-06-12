<?php

namespace Creativestyle\ProductBestsellersRankingExtension\Model\Config\Source;

class OrdersPeriod implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Get order period array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => 'All Orders'],
            ['value' => 1, 'label' => 'Orders from 7 days'],
            ['value' => 2, 'label' => 'Orders from 30 days'],
            ['value' => 3, 'label' => 'Orders from 1 year'],
        ];
    }
}