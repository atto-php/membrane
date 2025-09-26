<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Exception;

/**
 * @phpstan-import-type DTOArray from DTOConfig
 * @phpstan-type OperationArray array{
 *     handler?: class-string,
 *     dto?: class-string | DTOArray
 * }
 */
final class OperationManager
{
    /** @var class-string */
    private string $flattener;
    private bool $useFlattener;

    private ?DTOConfig $defaultDto = null;
    private ?string $defaultHandler;

    /** @var array<string, DTOConfig> */
    private array $operationDTOs = [];
    /** @var array<string, class-string> */
    private array $operationHandlers = [];

    /**
     * @param array{
     *     flattener?: class-string,
     *     useFlattener?: bool,
     *     default?: OperationArray,
     *     operationMap?: array<string, OperationArray>,
     * } $config
     */
    public function __construct(
        private readonly array $config,
    ) {
        $this->flattener = $config['flattener'] ?? Flattener::class;
        $this->useFlattener = $config['useFlattener'] ?? true;
        $this->defaultHandler = $this->config['default']['handler'] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->config['operationMap'][$id]);
    }

    public function dto(string $id): string
    {
        return $this->getDtoConfig($id)->dto;
    }

    public function usesFlattener(string $id): bool
    {
        return $this->getDtoConfig($id)->useFlattener;
    }

    public function flattener(string $id): string
    {
        return $this->getDtoConfig($id)->flattener;
    }

    /**
     * @return class-string
     * @throws \RuntimeException if operation has no defined handler
     */
    public function handler(string $id): string
    {
        if (!array_key_exists($id, $this->operationHandlers)) {
            $this->operationHandlers[$id] = $this->config['operationMap'][$id]['handler']
                ?? $this->defaultHandler
                ?? throw new \RuntimeException("No handler defined for operation $id");
        }

        return $this->operationHandlers[$id];
    }

    /**
     * @throws \RuntimeException if it fails to create DTOConfig
     */
    private function getDtoConfig(string $id): DTOConfig
    {
        if (!array_key_exists($id, $this->operationDTOs)) {
            $this->operationDTOs[$id] = (
                isset($this->config['operationMap'][$id]['dto'])
                    ? DTOConfig::create(
                        $this->config['operationMap'][$id]['dto'],
                        $this->getGlobalConfig(),
                    ) : $this->getDefaultDtoConfig())
                ??  throw new \RuntimeException("No DTO defined for operation $id");
        }

        return $this->operationDTOs[$id];
    }

    private function getDefaultDtoConfig(): ?DTOConfig
    {
        if (!isset($this->defaultDto) && isset($this->config['default']['dto'])) {
            $this->defaultDto = DTOConfig::create(
                $this->config['default']['dto'],
                $this->getGlobalConfig(),
            );
        }

        return $this->defaultDto;
    }

    /**
     * @return array{
     *     flattener: class-string,
     *     useFlattener: bool,
     * }
     */
    private function getGlobalConfig(): array
    {
        return [
            'flattener' => $this->flattener,
            'useFlattener' => $this->useFlattener
        ];
    }
}
