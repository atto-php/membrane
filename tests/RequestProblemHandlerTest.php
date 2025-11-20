<?php

declare(strict_types=1);

namespace Atto\Membrane\Tests;

use Atto\Membrane\RequestProblemHandler;
use Crell\ApiProblem\ApiProblem;
use Membrane\OpenAPI\Exception\CannotProcessSpecification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[\PHPUnit\Framework\Attributes\CoversClass(RequestProblemHandler::class)]
final class RequestProblemHandlerTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    #[DataProvider('provideErrorsToSupport')]
    public function itSupportsErrors(
        bool $expected,
        \Throwable $error,
    ): void {
        self::assertSame($expected, (new RequestProblemHandler())->supports($error));
    }

    #[Test]
    #[DataProvider('provideErrorsToHandle')]
    public function itHandlesErrors(
        ApiProblem $expected,
        \Throwable $error,
    ): void {
        self::assertEquals($expected, (new RequestProblemHandler())->handle($error));
    }

    /** @return \Generator<array{0:bool, 1:\Throwable}> */
    public static function provideErrorsToSupport(): \Generator
    {
        yield 'path not found' => [
            true,
            CannotProcessSpecification::pathNotFound('api.yml', '/pets'),
        ];

        yield 'method not found' => [
            true,
            CannotProcessSpecification::methodNotFound( 'DELETE'),
        ];

        yield 'bad request' => [
            false,
            new \RuntimeException(),
        ];
    }

    /** @return \Generator<array{0:ApiProblem, 1:\Throwable}> */
    public static function provideErrorsToHandle(): \Generator
    {
        yield 'path not found' => [
            (new ApiProblem('Not found', 'about:blank'))->setStatus(404),
            CannotProcessSpecification::pathNotFound('api.yml', '/pets'),
        ];

        yield 'method not found' => [
            (new ApiProblem('Method unsupported', 'about:blank'))->setStatus(405),
            CannotProcessSpecification::methodNotFound( 'DELETE'),
        ];

        yield 'bad request' => [
            (new ApiProblem('Bad Request', 'about:blank'))->setStatus(400),
            new CannotProcessSpecification('foo'),
        ];
    }
}
