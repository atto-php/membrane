<?php

declare(strict_types=1);

namespace Atto\Membrane\Application;

use Atto\Framework\Application\ApplicationInterface;
use Atto\Framework\Response\Builder;
use Atto\Membrane\OperationManager;
use Atto\Membrane\RequestParser;
use Atto\Psr7\ResponseEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Container\ContainerInterface;

final class MembraneOpenApi implements ApplicationInterface
{
    public function __construct(
        private ContainerInterface $container,
        private OperationManager $operationManager,
        private Builder $responseBuilder,
        private RequestParser $requestParser,
        private ResponseEmitter $responseEmitter,
    ) {
    }

    public function run(): void
    {
        try {
            $this->requestParser->parseFromGlobals();
            $processedRequest = $this->requestParser->getResult();
            $operation = $this->requestParser->getOperation();

            //@TODO handle auth and rbac
            $result = $this->container->get($this->operationManager->handler($operation))($processedRequest->value);
        } catch (\Throwable $result) {
        }

        $this->responseEmitter->emit($this->responseBuilder->buildResponse($result));
    }
}