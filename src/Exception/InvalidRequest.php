<?php

declare(strict_types=1);

namespace Heard\Framework\Exception;

use Atto\Framework\Response\Errors\HasApiProblem;
use Membrane\Renderer\JsonFlat;
use Membrane\Result\Result;

final class InvalidRequest extends \RuntimeException implements HasApiProblem
{
    private Result $result;
    private string $operationId;

    public static function fromResult(Result $result, string $operationId): self
    {
        $instance = new self();
        $instance->result = $result;
        $instance->operationId = $operationId;
        return $instance;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    public function getType(): ?string
    {
        return 'about:blank';
    }

    public function getTitle(): ?string
    {
        return 'Invalid Request';
    }

    public function getDetail(): ?string
    {
        return 'The request failed to validate';
    }

    public function getAdditionalInformation(): array
    {
        $renderer = new JsonFlat($this->result);
        return ['errors' => $renderer->toArray(), 'operationId' => $this->operationId];
    }

    public function getDebugInformation(): array
    {
        return [];
    }
}