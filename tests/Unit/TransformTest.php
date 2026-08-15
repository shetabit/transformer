<?php

namespace Shetabit\Transformer\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;
use Shetabit\Transformer\Exceptions\TransformerNotValidException;
use Shetabit\Transformer\Tests\Fixtures\CredentialsTransformer;
use Shetabit\Transformer\Tests\Fixtures\RecordingTransformer;
use Shetabit\Transformer\Tests\TestCase;

#[CoversClass(Transform::class)]
class TransformTest extends TestCase
{
    public function testItStartsWithoutData() : void
    {
        $transform = new Transform();

        $this->assertSame([], $transform->getOriginalData());
        $this->assertSame([], $transform->getTransformedData());
        $this->assertNull($transform->getTransformer());
    }

    public function testItTakesTheOriginalDataInTheConstructor() : void
    {
        $this->assertSame(['a' => 1], new Transform(['a' => 1])->getOriginalData());
    }

    public function testTheOriginalDataCanBeSetAfterwards() : void
    {
        $transform = new Transform(['a' => 1]);

        $this->assertSame($transform, $transform->setOriginalData(['b' => 2]));
        $this->assertSame(['b' => 2], $transform->getOriginalData());
    }

    public function testUseSetsTheTransformer() : void
    {
        $transform = new Transform();
        $transformer = new Transformer();

        $this->assertSame($transform, $transform->use($transformer));
        $this->assertSame($transformer, $transform->getTransformer());
    }

    public function testSetTransformerSetsTheTransformer() : void
    {
        $transform = new Transform();
        $transformer = new Transformer();

        $this->assertSame($transform, $transform->setTransformer($transformer));
        $this->assertSame($transformer, $transform->getTransformer());
    }

    public function testGetRunsTheTransformerItIsHanded() : void
    {
        $transform = new Transform(['u' => 'mahdikhanzadi', 'p' => '246810']);

        $this->assertSame(
            ['username' => 'mahdikhanzadi', 'password' => '246810'],
            $transform->get(new CredentialsTransformer()),
        );
    }

    public function testGetRunsTheTransformerItWasGivenBefore() : void
    {
        $transform = new Transform(['u' => 'mahdikhanzadi', 'p' => '246810'])
            ->use(new CredentialsTransformer());

        $this->assertSame(
            ['username' => 'mahdikhanzadi', 'password' => '246810'],
            $transform->get(),
        );
    }

    public function testTheTransformerHandedToGetReplacesTheOneSetBefore() : void
    {
        $first = new RecordingTransformer();
        $second = new RecordingTransformer();

        $transform = new Transform(['a' => 1])->use($first);
        $transform->get($second);

        $this->assertSame([], $first->calls);
        $this->assertSame([['a' => 1]], $second->calls);
        $this->assertSame($second, $transform->getTransformer());
    }

    public function testGetWithoutATransformerThrows() : void
    {
        $this->expectException(TransformerNotValidException::class);
        $this->expectExceptionMessage('Transformer not found');

        new Transform(['a' => 1])->get();
    }

    public function testGetHandsTheOriginalDataToTheTransformer() : void
    {
        $transformer = new RecordingTransformer();

        new Transform(['a' => 1])->get($transformer);

        $this->assertSame([['a' => 1]], $transformer->calls);
    }

    public function testGetRemembersWhatItProduced() : void
    {
        $transform = new Transform(['u' => 'mahdi', 'p' => 'secret']);

        $this->assertSame([], $transform->getTransformedData());

        $transform->get(new CredentialsTransformer());

        $this->assertSame(
            ['username' => 'mahdi', 'password' => 'secret'],
            $transform->getTransformedData(),
        );
    }

    public function testTheSameTransformCanBeRunOverNewData() : void
    {
        $transform = new Transform(['u' => 'first', 'p' => 'one'])->use(new CredentialsTransformer());

        $this->assertSame(['username' => 'first', 'password' => 'one'], $transform->get());
        $this->assertSame(
            ['username' => 'second', 'password' => 'two'],
            $transform->setOriginalData(['u' => 'second', 'p' => 'two'])->get(),
        );
    }

    public function testGetKeepsTheTransformerWhenItIsHandedNull() : void
    {
        $transform = new Transform(['u' => 'mahdi', 'p' => 'secret'])->use(new CredentialsTransformer());
        $nothing = null;

        $this->assertSame(
            ['username' => 'mahdi', 'password' => 'secret'],
            $transform->get($nothing),
        );
    }
}
