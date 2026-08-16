<?php

namespace Shetabit\Transformer\Tests\Feature;

use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Tests\TestCase;

class KeyCollisionTest extends TestCase
{
    public function testTwoColumnsThatWereMixedUpCanBeSwappedBack() : void
    {
        $rows = [
            ['id' => 1, 'first_name' => 'khanzadi', 'last_name' => 'mahdi'],
            ['id' => 2, 'first_name' => 'ahmadi', 'last_name' => 'ali'],
        ];

        $transform = new Transform()->use(new Transformer([
            'first_name' => 'last_name',
            'last_name' => 'first_name',
        ]));

        $swapped = array_map(
            static fn (array $row): array => $transform->setOriginalData($row)->get(),
            $rows,
        );

        $this->assertSame([
            ['id' => 1, 'last_name' => 'khanzadi', 'first_name' => 'mahdi'],
            ['id' => 2, 'last_name' => 'ahmadi', 'first_name' => 'ali'],
        ], $swapped);
    }

    public function testAWholeRowIsShiftedOnePositionAlong() : void
    {
        $misalignedRow = [
            'name' => 'Tehran',
            'city' => '1966733123',
            'postal_code' => '+982112345678',
            'phone' => 'Shetabit',
        ];

        $transformer = new Transformer([
            'name' => 'phone',
            'city' => 'name',
            'postal_code' => 'city',
            'phone' => 'postal_code',
        ]);

        $this->assertSame([
            'phone' => 'Tehran',
            'name' => '1966733123',
            'city' => '+982112345678',
            'postal_code' => 'Shetabit',
        ], new Transform($misalignedRow)->get($transformer));
    }

    public function testARenamedKeyOverridesAKeyThatAlreadyCarriesTheDestinationName() : void
    {
        $payload = [
            'legacy_total' => 12900,
            'total' => 0,
        ];

        $transformed = new Transform($payload)->get(new Transformer(['legacy_total' => 'total']));

        $this->assertSame(['total' => 12900], $transformed);
    }

    public function testARenamedKeyKeepsTheIntegerKeysOfTheRestOfThePayload() : void
    {
        $payload = [
            10 => 'ten',
            'ord_id' => 4711,
            20 => 'twenty',
        ];

        $this->assertSame(
            [10 => 'ten', 'id' => 4711, 20 => 'twenty'],
            new Transform($payload)->get(new Transformer(['ord_id' => 'id'])),
        );
    }

    public function testNestedRowsAreSwappedTheSameWayTheOuterOnesAre() : void
    {
        $payload = [
            'first_name' => 'khanzadi',
            'last_name' => 'mahdi',
            'contacts' => [
                ['first_name' => 'ahmadi', 'last_name' => 'ali'],
                ['first_name' => 'rezaei', 'last_name' => 'reza'],
            ],
        ];

        $transformer = new Transformer([
            'first_name' => 'last_name',
            'last_name' => 'first_name',
        ], recursive: true);

        $this->assertSame([
            'last_name' => 'khanzadi',
            'first_name' => 'mahdi',
            'contacts' => [
                ['last_name' => 'ahmadi', 'first_name' => 'ali'],
                ['last_name' => 'rezaei', 'first_name' => 'reza'],
            ],
        ], new Transform($payload)->get($transformer));
    }
}
