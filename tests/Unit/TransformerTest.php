<?php

namespace Shetabit\Transformer\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Contracts\TransformerInterface;
use Shetabit\Transformer\Exceptions\InvalidFormatException;
use Shetabit\Transformer\Tests\TestCase;

#[CoversClass(Transformer::class)]
class TransformerTest extends TestCase
{
    public function testItImplementsTheTransformerContract() : void
    {
        $this->assertInstanceOf(TransformerInterface::class, new Transformer());
    }

    public function testItRenamesTheKeysOfTheFormat() : void
    {
        $transformer = new Transformer([
            'f_name' => 'first_name',
            'l_name' => 'last_name',
        ]);

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            $transformer->transform(['f_name' => 'mahdi', 'l_name' => 'khanzadi']),
        );
    }

    public function testItLeavesTheKeysTheFormatDoesNotMention() : void
    {
        $transformer = new Transformer(['f_name' => 'first_name']);

        $this->assertSame(
            ['first_name' => 'mahdi', 'age' => 30],
            $transformer->transform(['f_name' => 'mahdi', 'age' => 30]),
        );
    }

    public function testItKeepsAKeyInItsPlace() : void
    {
        $transformer = new Transformer(['b' => 'second']);

        $this->assertSame(
            ['a' => 1, 'second' => 2, 'c' => 3],
            $transformer->transform(['a' => 1, 'b' => 2, 'c' => 3]),
        );
    }

    public function testItLeavesTheDataAloneWithoutAFormat() : void
    {
        $data = ['a' => 1, 'b' => [2, 3], 'c' => null];

        $this->assertSame($data, new Transformer()->transform($data));
    }

    public function testItTransformsAnEmptyArray() : void
    {
        $this->assertSame([], new Transformer(['a' => 'b'])->transform([]));
    }

    public function testItKeepsTheValuesAsTheyAre() : void
    {
        $object = new \stdClass();

        $transformed = new Transformer(['a' => 'x'])->transform([
            'a' => $object,
            'b' => null,
            'c' => [1, 2],
            'd' => '007',
        ]);

        $this->assertSame($object, $transformed['x']);
        $this->assertNull($transformed['b']);
        $this->assertSame([1, 2], $transformed['c']);
        $this->assertSame('007', $transformed['d']);
    }

    public function testItSwapsTwoKeys() : void
    {
        $transformer = new Transformer(['a' => 'b', 'b' => 'a']);

        $this->assertSame(
            ['b' => 1, 'a' => 2],
            $transformer->transform(['a' => 1, 'b' => 2]),
        );
    }

    public function testItRotatesThreeKeys() : void
    {
        $transformer = new Transformer(['a' => 'b', 'b' => 'c', 'c' => 'a']);

        $this->assertSame(
            ['b' => 1, 'c' => 2, 'a' => 3],
            $transformer->transform(['a' => 1, 'b' => 2, 'c' => 3]),
        );
    }

    public function testARenamedKeyIsNotRenamedAgainByALaterPairOfTheFormat() : void
    {
        $transformer = new Transformer(['a' => 'b', 'b' => 'c']);

        $this->assertSame(
            ['b' => 1, 'c' => 2],
            $transformer->transform(['a' => 1, 'b' => 2]),
        );
    }

    public function testARenamedKeyWinsOverAnUntouchedKeyOfTheSameName() : void
    {
        $transformer = new Transformer(['a' => 'b']);

        $this->assertSame(['b' => 1], $transformer->transform(['a' => 1, 'b' => 2]));
    }

    public function testARenamedKeyWinsWhereverTheUntouchedKeyStands() : void
    {
        $transformer = new Transformer(['a' => 'b']);

        $this->assertSame(['b' => 1], $transformer->transform(['b' => 2, 'a' => 1]));
    }

    public function testOfTwoKeysRenamedToTheSameNameTheFirstOneWins() : void
    {
        $transformer = new Transformer(['a' => 'x', 'b' => 'x']);

        $this->assertSame(['x' => 1], $transformer->transform(['a' => 1, 'b' => 2]));
    }

    public function testItKeepsIntegerKeysAsTheyAre() : void
    {
        $transformer = new Transformer(['a' => 'b']);

        $this->assertSame(
            [5 => 'five', 'b' => 1, 9 => 'nine'],
            $transformer->transform([5 => 'five', 'a' => 1, 9 => 'nine']),
        );
    }

    public function testItKeepsTheIndexesOfAList() : void
    {
        $transformer = new Transformer(['a' => 'b']);

        $this->assertSame(
            [0 => 'zero', 1 => 'one', 2 => 'two'],
            $transformer->transform([0 => 'zero', 1 => 'one', 2 => 'two']),
        );
    }

    public function testItRenamesAnIntegerKey() : void
    {
        $transformer = new Transformer([7 => 'seven']);

        $this->assertSame(['seven' => 'value'], $transformer->transform([7 => 'value']));
    }

    public function testItRenamesToAnIntegerKey() : void
    {
        $transformer = new Transformer(['a' => 3]);

        $this->assertSame([3 => 'value'], $transformer->transform(['a' => 'value']));
    }

    public function testANumericStringKeyOfTheFormatMatchesTheIntegerKeyOfTheData() : void
    {
        $transformer = new Transformer()->from('7')->to('seven');

        $this->assertSame(['seven' => 'value'], $transformer->transform([7 => 'value']));
    }

    public function testItDoesNotRenameAKeyThatOnlyLooksLikeTheOneOfTheFormat() : void
    {
        $transformer = new Transformer(['1' => 'one']);

        $this->assertSame(['01' => 'value'], $transformer->transform(['01' => 'value']));
    }

    public function testFromAndToTakeSingleKeys() : void
    {
        $transformer = new Transformer()
            ->from('f_name')->to('first_name')
            ->from('l_name')->to('last_name');

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            $transformer->transform(['f_name' => 'mahdi', 'l_name' => 'khanzadi']),
        );
    }

    public function testFromAndToTakeListsOfKeys() : void
    {
        $transformer = new Transformer()
            ->from(['f_name', 'l_name'])
            ->to(['first_name', 'last_name']);

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            $transformer->transform(['f_name' => 'mahdi', 'l_name' => 'khanzadi']),
        );
    }

    public function testFromAndToCanBeMixedWithTheConstructorFormat() : void
    {
        $transformer = new Transformer(['f_name' => 'first_name'])
            ->from('l_name')->to('last_name');

        $this->assertSame(
            ['first_name' => 'mahdi', 'last_name' => 'khanzadi'],
            $transformer->transform(['f_name' => 'mahdi', 'l_name' => 'khanzadi']),
        );
    }

    public function testFromAndToIgnoreTheKeysOfTheListsTheyAreGiven() : void
    {
        $transformer = new Transformer()
            ->from(['a' => 'f_name', 'b' => 'l_name'])
            ->to(['z' => 'first_name', 'y' => 'last_name']);

        $this->assertSame(['f_name' => 'first_name', 'l_name' => 'last_name'], $transformer->getFormat());
    }

    public function testFromAndToAreFluent() : void
    {
        $transformer = new Transformer();

        $this->assertSame($transformer, $transformer->from('a'));
        $this->assertSame($transformer, $transformer->to('b'));
        $this->assertSame($transformer, $transformer->recursive());
    }

    public function testTheLastMappingOfAKeyWins() : void
    {
        $transformer = new Transformer()
            ->from('a')->to('x')
            ->from('a')->to('y');

        $this->assertSame(['y' => 1], $transformer->transform(['a' => 1]));
    }

    public function testGetFormatPairsTheSourceKeysWithTheDestinationKeys() : void
    {
        $transformer = new Transformer(['a' => 'x'])->from('b')->to('y');

        $this->assertSame(['a' => 'x', 'b' => 'y'], $transformer->getFormat());
    }

    public function testItRefusesAFormatWithMoreSourcesThanDestinations() : void
    {
        $transformer = new Transformer()->from('a')->from('b')->to('x');

        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('2 source key(s) are mapped onto 1 destination key(s)');

        $transformer->transform(['a' => 1]);
    }

    public function testItRefusesAFormatWithMoreDestinationsThanSources() : void
    {
        $transformer = new Transformer()->from('a')->to('x')->to('y');

        $this->expectException(InvalidFormatException::class);

        $transformer->transform(['a' => 1]);
    }

    public function testItIsNotRecursiveByDefault() : void
    {
        $transformer = new Transformer(['a' => 'x']);

        $this->assertFalse($transformer->isRecursive());
        $this->assertSame(
            ['x' => ['a' => 1]],
            $transformer->transform(['a' => ['a' => 1]]),
        );
    }

    public function testItRenamesTheKeysOfNestedArraysWhenItIsRecursive() : void
    {
        $transformer = new Transformer(['a' => 'x'], recursive: true);

        $this->assertTrue($transformer->isRecursive());
        $this->assertSame(
            ['x' => ['x' => ['x' => 1]]],
            $transformer->transform(['a' => ['a' => ['a' => 1]]]),
        );
    }

    public function testItRenamesTheKeysOfTheArraysOfAListWhenItIsRecursive() : void
    {
        $transformer = new Transformer()->from('sku')->to('code')->recursive();

        $this->assertSame(
            ['items' => [['code' => 'A-1'], ['code' => 'B-7']]],
            $transformer->transform(['items' => [['sku' => 'A-1'], ['sku' => 'B-7']]]),
        );
    }

    public function testRecursionCanBeTurnedOffAgain() : void
    {
        $transformer = new Transformer(['a' => 'x'], recursive: true)->recursive(false);

        $this->assertFalse($transformer->isRecursive());
        $this->assertSame(
            ['x' => ['a' => 1]],
            $transformer->transform(['a' => ['a' => 1]]),
        );
    }

    public function testItSwapsKeysOfANestedArrayWhenItIsRecursive() : void
    {
        $transformer = new Transformer(['a' => 'b', 'b' => 'a'], recursive: true);

        $this->assertSame(
            ['b' => ['b' => 1, 'a' => 2], 'a' => 3],
            $transformer->transform(['a' => ['a' => 1, 'b' => 2], 'b' => 3]),
        );
    }

    public function testItCanBeRunTwiceWithTheSameResult() : void
    {
        $transformer = new Transformer(['f_name' => 'first_name']);
        $data = ['f_name' => 'mahdi'];

        $this->assertSame($transformer->transform($data), $transformer->transform($data));
    }
}
