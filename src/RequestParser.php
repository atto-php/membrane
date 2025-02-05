<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Atto\Membrane\Exception\InvalidRequest;
use Membrane\Attribute\ClassWithAttributes;
use Membrane\Membrane;
use Membrane\OpenAPI\Specification\Request;
use Membrane\Result\Result;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

final class RequestParser
{
    private Result $result;
    private string $operation;

    public function __construct(
        private ServerRequestInterface $request,
        private OperationManager $operationManager,
        private Membrane $membrane,
        private string $openAPISpec
    ) {
    }

    public function parseFromGlobals(): void
    {
        $requestSpec = Request::fromPsr7($this->openAPISpec, $this->request);

        $result = $this->membrane->process($this->request, $requestSpec);
        $this->operation = $result->value['request']['operationId'];

        if (!$result->isValid()) {
            throw InvalidRequest::fromResult($result, $this->operation);
        }

        $specifications = [];

        if ($this->operationManager->usesFlattener($this->operation)) {
            $specifications[] = new ClassWithAttributes($this->operationManager->flattener($this->operation));
        }

        $specifications[] = new ClassWithAttributes($this->operationManager->dto($this->operation));

        $this->result = $this->membrane->process($result->value, ...$specifications);

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
