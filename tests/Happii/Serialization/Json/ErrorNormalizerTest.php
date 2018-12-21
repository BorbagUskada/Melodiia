<?php

namespace Biig\Happii\Test\Serialization\Json;

use Biig\Happii\Response\AbstractUserDataErrorResponse;
use Biig\Happii\Response\Model\UserDataError;
use Biig\Happii\Serialization\Json\ErrorNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ErrorNormalizerTest extends TestCase
{
    public function testItIsSymfonyNormalizer()
    {
        $normalizer = new ErrorNormalizer();
        $this->assertInstanceOf(NormalizerInterface::class, $normalizer);
    }

    public function testItSupportsOnlyResponse()
    {
        $normalizer = new ErrorNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($this->getDummyError()));
        $this->assertFalse($normalizer->supportsNormalization([]));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }

    public function testItNormalizeCorrectly()
    {
        $normalizer = new ErrorNormalizer();

        $this->assertEquals(
            ['violations' => ['foo' => ['bar']]],
            $normalizer->normalize($this->getDummyError())
        );
    }

    private function getDummyError()
    {
        return new DummyError([new UserDataError('foo', ['bar'])]);
    }
}

class DummyError extends AbstractUserDataErrorResponse
{
    private $errors;
    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}