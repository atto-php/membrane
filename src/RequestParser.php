<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Heard\Framework\Exception\InvalidRequest;
use Membrane\Attribute\ClassWithAttributes;
use Membrane\Membrane;
use Membrane\OpenAPI\Specification\Request;
use Membrane\Result\Result;
use Nyholm\Psr7Server\ServerRequestCreator;

final class RequestParser
{
    private Result $result;
    private string $operation;

    public function __construct(
        private ServerRequestCreator $requestCreator,
        private OperationManager $operationManager,
        private Membrane $membrane,
        private string $openAPISpec
    ) {
    }

    public function parseFromGlobals(): void
    {
        $serverRequest = $this->requestCreator->fromGlobals();

        $requestSpec = Request::fromPsr7($this->openAPISpec, $serverRequest);
        $this->operation = $requestSpec->pathItem->getOperations()[strtolower($serverRequest->getMethod())]->operationId;

        $specifications = [$requestSpec];

        if ($this->operationManager->usesFlattener($this->operation)) {
            $specifications[] = new ClassWithAttributes($this->operationManager->flattener($this->operation));
        }

        $specifications[] = new ClassWithAttributes($this->operationManager->dto($this->operation));

        $this->result = $this->membrane->process($serverRequest, ...$specifications);

        if (!$this->result->isValid()) {
            throw InvalidRequest::fromResult($this->result, $this->operation);
        }
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

}