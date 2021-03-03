<?php

namespace Lamoda\AtolClient\Tests\Unit\Converter;

use Lamoda\AtolClient\Converter\ObjectConverter;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @group unit
 * @covers \Lamoda\AtolClient\Converter\ObjectConverter
 */
class ObjectConverterTest extends TestCase
{
    /**
     * @var SerializerInterface | MockObject
     */
    private $serializer;

    /**
     * @var ValidatorInterface | MockObject
     */
    private $validator;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->objectConverter = new ObjectConverter($this->serializer, $this->validator);
    }

    public function testGetRequestString()
    {
        $object = (object) [];
        $json = '{}';

        /* @see ObjectConverter::assertValid() */
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($object)
            ->willReturn([]);

        /* @see ObjectConverter::serializeBodyObject() */
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($object, 'atol_client', $this->anything())
            ->willReturn($json);

        $result = $this->objectConverter->getRequestString($object);
        $this->assertSame($json, $result);
    }

    public function testGetRequestStringParseException()
    {
        $this->expectException(\Lamoda\AtolClient\Exception\ParseException::class);

        /* @see ObjectConverter::assertValid() */
        $this->validator
            ->method('validate')
            ->willReturn([]);

        /* @see ObjectConverter::serializeBodyObject() */
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->willThrowException(new \RuntimeException());

        $this->objectConverter->getRequestString((object) []);
    }

    public function testGetRequestStringInvalid()
    {
        $this->expectException(\Lamoda\AtolClient\Exception\ValidationException::class);
        $this->expectExceptionCode(2);

        $object = (object) [];
        $errors = $this->createMock(ConstraintViolationListInterface::class);

        /* @see ObjectConverter::assertValid() */
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($object)
            ->willReturn($errors);
        $errors
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->objectConverter->getRequestString($object);
    }

    public function testGetResponseObject()
    {
        $class = 'class';
        $json = 'json';
        $object = (object) [];

        /* @see ObjectConverter::deserialize() */
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($json, $class, 'atol_client')
            ->willReturn($object);
        /* @see ObjectConverter::assertValid() */
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($object)
            ->willReturn([]);

        $result = $this->objectConverter->getResponseObject($class, $json);
        $this->assertSame($object, $result);
    }

    public function testGetResponseObjectParseException()
    {
        $this->expectException(\Lamoda\AtolClient\Exception\ParseException::class);
        $this->expectExceptionCode(2);

        /* @see ObjectConverter::deserialize() */
        $this->serializer
            ->method('deserialize')
            ->willThrowException(new \RuntimeException());

        $this->objectConverter->getResponseObject('class', 'json');
    }

    public function testGetResponseObjectInvalid()
    {
        $this->expectException(\Lamoda\AtolClient\Exception\ValidationException::class);
        $this->expectExceptionCode(2);

        $errors = $this->createMock(ConstraintViolationListInterface::class);

        /* @see ObjectConverter::assertValid() */
        $this->serializer
            ->method('deserialize')
            ->willReturn((object) []);
        $this->validator
            ->method('validate')
            ->willReturn($errors);
        $errors
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->objectConverter->getResponseObject('class', 'json');
    }
}
