<?php

namespace Shetabit\Transformer\Tests\Feature;

use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Tests\Fixtures\UpperCaseKeysTransformer;
use Shetabit\Transformer\Tests\TestCase;

class NestedPayloadTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private function format() : array
    {
        return [
            'ord_id' => 'id',
            'ord_state' => 'status',
            'cust' => 'customer',
            'f_name' => 'first_name',
            'l_name' => 'last_name',
            'addr' => 'address',
            'st' => 'street',
            'cty' => 'city',
            'zip' => 'postal_code',
            'itms' => 'items',
            'sku' => 'code',
            'qty' => 'quantity',
            'prc' => 'price',
            'ttl' => 'total',
        ];
    }

    /**
     * @return array<array-key, mixed>
     */
    private function renamedOrder() : array
    {
        return [
            'id' => 4711,
            'status' => 'paid',
            'customer' => [
                'first_name' => 'mahdi',
                'last_name' => 'khanzadi',
                'address' => [
                    'street' => 'Valiasr',
                    'city' => 'Tehran',
                    'postal_code' => '1966733123',
                ],
            ],
            'items' => [
                [
                    'code' => 'A-1',
                    'quantity' => 2,
                    'price' => 1500,
                ],
                [
                    'code' => 'B-7',
                    'quantity' => 1,
                    'price' => 9900,
                ],
            ],
            'total' => 12900,
        ];
    }

    public function testARecursiveTransformerRenamesTheWholeTree() : void
    {
        $transformer = new Transformer($this->format(), recursive: true);

        $this->assertSame(
            $this->renamedOrder(),
            new Transform($this->orderPayload())->get($transformer),
        );
    }

    public function testTheItemsKeepTheirIndexes() : void
    {
        $transformed = new Transform($this->orderPayload())
            ->get(new Transformer($this->format(), recursive: true));

        $this->assertSame([0, 1], array_keys($transformed['items']));
    }

    public function testAFlatTransformerLeavesTheNestedArraysAlone() : void
    {
        $payload = $this->orderPayload();

        $transformed = new Transform($payload)->get(new Transformer($this->format()));

        $this->assertSame(
            ['id', 'status', 'customer', 'items', 'total'],
            array_keys($transformed),
        );
        $this->assertSame($payload['cust'], $transformed['customer']);
        $this->assertSame($payload['itms'], $transformed['items']);
    }

    public function testTheTransformationCanBeUndoneWithTheFlippedFormat() : void
    {
        $payload = $this->orderPayload();

        $transformed = new Transform($payload)
            ->get(new Transformer($this->format(), recursive: true));

        $restored = new Transform($transformed)
            ->get(new Transformer(array_flip($this->format()), recursive: true));

        $this->assertSame($payload, $restored);
    }

    public function testTheOutputOfOneTransformerIsTheInputOfTheNext() : void
    {
        $transform = new Transform($this->orderPayload());

        $renamed = $transform->get(new Transformer($this->format(), recursive: true));

        $this->assertSame(
            ['ID', 'STATUS', 'CUSTOMER', 'ITEMS', 'TOTAL'],
            array_keys($transform->setOriginalData($renamed)->get(new UpperCaseKeysTransformer())),
        );
    }

    public function testAKeyThatIsNotInThePayloadIsNotAdded() : void
    {
        $transformer = new Transformer(['missing' => 'added'], recursive: true);

        $this->assertSame(
            $this->orderPayload(),
            new Transform($this->orderPayload())->get($transformer),
        );
    }

    public function testTheOriginalPayloadIsLeftAsItWas() : void
    {
        $payload = $this->orderPayload();
        $transform = new Transform($payload);

        $transform->get(new Transformer($this->format(), recursive: true));

        $this->assertSame($payload, $transform->getOriginalData());
    }
}
