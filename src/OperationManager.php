<?php

declare(strict_types=1);

namespace Atto\Membrane;

final class OperationManager
{
    /* @var array<string, array<string, mixed>> */
    private array $operationMap;

    private bool $useFlattener;
    private ?string $flattener;

    /**
     * @var array{
     *    handler: ?class-string,
     *    dto: ?class-string,
     *    flattener: ?class-string,
     *    useFlattener: bool,
     * }
     */
    private array $default;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->flattener = $config['flattener']
            ?? Flattener::class;
        $this->useFlattener = $config['useFlattener']
            ?? true;

        $this->default['handler'] = $config['default']['handler']
            ?? $config['defaultHandler']
            ?? null;
        $this->default['dto'] = $config['default']['dto']
            ?? $config['defaultDto']
            ?? null;
        $this->default['useFlattener'] = $config['default']['useFlattener']
            ?? $config['defaultUseFlattener']
            ?? true;
        $this->default['flattener'] = $config['default']['flattener']
            ?? $config['defaultFlattener']
            ?? null;

        $this->operationMap = $config['operationMap']
            ?? [];
    }

    public function has(string $id): bool
    {
        return isset($this->operationMap[$id]);
    }

    public function dto(string $id): string
    {
        return $this->operationMap[$id]['dto']
            ?? $this->default['dto']
            ?? throw new \Exception('No DTO defined for operation ' . $id);
    }

    public function usesFlattener(string $id): bool
    {
        if ($this->dto($id) === $this->default['dto']) {
            return $this->default['useFlattener'];
        }

        return $this->operationMap[$id]['useFlattener']
            ?? $this->useFlattener;
    }

    public function flattener(string $id): string
    {
        if ($this->dto($id) === $this->default['dto']) {
            return $this->default['flattener']
                ?? $this->flattener;
        }

        return $this->operationMap[$id]['flattener']
            ?? $this->flattener;
    }

    /* @return class-string */
    public function handler(string $id): string
    {
        return $this->operationMap[$id]['handler']
            ?? $this->default['handler']
            ?? throw new \Exception('No handler defined for operation ' . $id);
    }
}
