<?php

declare(strict_types=1);

namespace Atto\Membrane;

/**
 * @internal
 * @phpstan-type DTOArray array{
 *     class?: class-string,
 *     flattener?: class-string,
 *     useFlattener?: bool,
 * }
 */
final readonly class DTOConfig
{
    public function __construct(
        public string $dto,
        public string $flattener,
        public bool $useFlattener,
    ) {}

    /**
     * @param DTOArray|class-string $config
     * @param array{flattener: class-string, useFlattener: bool} $globalConfig
     *
     * @throws \RuntimeException if config fails to define a dto class
     */
    public static function create(array|string $config, array $globalConfig): DTOConfig
    {
        if (is_string($config)) {
            return new self($config, $globalConfig['flattener'], $globalConfig['useFlattener']);
        }

        return new self(
            $config['class']
                ?? throw new \RuntimeException(sprintf(
                    '"dto" fields MUST be %s OR %s',
                    'a class-string',
                    'an array containing the "class" field',
                )),
            $config['flattener'] ?? $globalConfig['flattener'],
            $config['useFlattener'] ?? $globalConfig['useFlattener'],
        );
    }
}
