<?php

declare(strict_types=1);

namespace Atto\Membrane\Tests;

use Atto\Membrane\OperationManager;
use Atto\Membrane\RequestParser;
use Membrane\Attribute;
use Membrane\Membrane;
use Membrane\OpenAPI\Builder\RequestBuilder;
use Membrane\OpenAPI\Exception\CannotProcessSpecification;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;

#[\PHPUnit\Framework\Attributes\CoversClass(RequestParser::class)]
final class RequestParserTest extends \PHPUnit\Framework\TestCase
{

    #[Test]
    #[DataProvider('provideErroneousRequests')]
    public function itThrowsAppropriateExceptions(
        \Exception $expected,
        ServerRequestInterface $request,
        OperationManager $operationManager,
        string $api,
    ): void {
        $membrane = new Membrane(new Attribute\Builder(), new RequestBuilder());
        $sut = new RequestParser($request, $operationManager, $membrane, $api);

        self::expectExceptionObject($expected);

        $sut->parseFromGlobals();
    }

    /**
     * @return \Generator<array{
     *     bool,
     *     array<string, mixed>,
     *     string,
     *  }>
     */
    public static function provideErroneousRequests(): \Generator
    {
        yield 'path not found' => [
            CannotProcessSpecification::pathNotFound(
                'petstore.yml',
                '/one-thousand-feral-cats'
            ),
            new ServerRequest('get', '/one-thousand-feral-cats'),
            new OperationManager([]),
            self::getApiFilePath('petstore.yml'),
        ];

        yield 'method not found' => [
            CannotProcessSpecification::methodNotFound('trace'),
            new ServerRequest('trace', '/pets'),
            new OperationManager([]),
            self::getApiFilePath('petstore.yml'),
        ];
    }

    private static function getApiFilePath(string $filename): string
    {
        return __DIR__ . "/fixture/$filename";
    }
}
