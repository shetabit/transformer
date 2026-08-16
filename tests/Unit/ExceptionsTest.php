<?php

namespace Shetabit\Transformer\Tests\Unit;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Shetabit\Transformer\Exceptions\InvalidFormatException;
use Shetabit\Transformer\Exceptions\TransformerException;
use Shetabit\Transformer\Exceptions\TransformerNotValidException;
use Shetabit\Transformer\Tests\TestCase;

#[CoversClass(TransformerException::class)]
#[CoversClass(TransformerNotValidException::class)]
#[CoversClass(InvalidFormatException::class)]
class ExceptionsTest extends TestCase
{
    public function testEveryExceptionOfThePackageCanBeCaughtAtOnce() : void
    {
        $this->assertInstanceOf(TransformerException::class, new TransformerNotValidException());
        $this->assertInstanceOf(TransformerException::class, new InvalidFormatException());
    }

    public function testTheBaseExceptionIsAnOrdinaryException() : void
    {
        $this->assertInstanceOf(Exception::class, new TransformerException());
    }

    public function testAnExceptionCarriesItsMessage() : void
    {
        $this->assertSame('gone wrong', new TransformerNotValidException('gone wrong')->getMessage());
    }
}
