<?php

namespace Shetabit\Transformer\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The payload an api of the shape this package is written for hands over: keys in
     * the naming of one system, nested objects and a list of objects.
     *
     * @return array<array-key, mixed>
     */
    protected function orderPayload() : array
    {
        return [
            'ord_id' => 4711,
            'ord_state' => 'paid',
            'cust' => [
                'f_name' => 'mahdi',
                'l_name' => 'khanzadi',
                'addr' => [
                    'st' => 'Valiasr',
                    'cty' => 'Tehran',
                    'zip' => '1966733123',
                ],
            ],
            'itms' => [
                [
                    'sku' => 'A-1',
                    'qty' => 2,
                    'prc' => 1500,
                ],
                [
                    'sku' => 'B-7',
                    'qty' => 1,
                    'prc' => 9900,
                ],
            ],
            'ttl' => 12900,
        ];
    }
}
