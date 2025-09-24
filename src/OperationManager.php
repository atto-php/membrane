<?php

declare(strict_types=1);

namespace Atto\Membrane;

final class OperationManager
{
    /* @var array<string, array<string, mixed>> */
    private array $operationMap;
    private ?string $defaultHandler;
    private ?string $defaultDto;
    private bool $defaultUseFlattener;
    /* @var class-string */
    private ?string $defaultFlattener;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->operationMap = $config['operationMap']
            ?? [];
        $this->defaultHandler = $config['default']['handler']
            ?? $config['defaultHandler']
            ?? null;
        $this->defaultDto = $config['default']['dto']
            ?? $config['defaultDto']
            ?? null;
        $this->defaultUseFlattener = $config['default']['useFlattener']
            ?? $config['defaultUseFlattener']
            ?? true;
        $this->defaultFlattener = $config['default']['flattener']
            ?? $config['defaultFlattener']
            ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->operationMap[$id]);
    }

    public function dto(string $id): string
    {
        return $this->operationMap[$id]['dto']
            ?? $this->defaultDto
            ?? throw new \Exception('No DTO defined for operation ' . $id);
    }

    public function usesFlattener(string $id): bool
    {
        return $this->operationMap[$id]['useFlattener']
            ?? $this->defaultUseFlattener;
    }

    public function flattener(string $id): string
    {
        return $this->operationMap[$id]['flattener']
            ?? Flattener::class;
    }

    /* @return class-string */
    public function handler(string $id): string
    {
        return $this->operationMap[$id]['handler']
            ?? $this->defaultHandler
            ?? throw new \Exception('No handler defined for operation ' . $id);
    }
}
