<?php

namespace Lamoda\AtolClient\Tests\Unit\Exception;

use Lamoda\AtolClient\Exception\UnknownStatusException;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @covers \Lamoda\AtolClient\Exception\UnknownStatusException
 */
class UnknownStatusExceptionTest extends TestCase
{
    public function testBecauseException()
    {
        $this->expectException(\Lamoda\AtolClient\Exception\UnknownStatusException::class);

        throw new UnknownStatusException();
    }
}
