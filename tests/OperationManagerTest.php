<?php

declare(strict_types=1);

namespace Atto\Membrane\Tests;

use Atto\Membrane\Flattener;
use Atto\Membrane\OperationManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[\PHPUnit\Framework\Attributes\CoversClass(OperationManager::class)]
final class OperationManagerTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    #[DataProvider('provideConfigsWithOperations')]
    public function itDeterminesIfItHasOperation(
        bool $expected,
        array $config,
        string $id,
    ): void {
        self::assertSame($expected, (new OperationManager($config))->has($id));
    }

    #[Test]
    #[DataProvider('provideConfigsWithDtos')]
    public function itGetsDto(
        string $expected,
        array $config,
        string $id,
    ): void {
        self::assertSame($expected, (new OperationManager($config))->dto($id));
    }

    #[Test]
    #[DataProvider('provideConfigsUsingFlatteners')]
    public function itDeterminesIfItUsesFlattener(
        bool $expected,
        array $config,
        string $id,
    ): void {
        self::assertSame($expected, (new OperationManager($config))->usesFlattener($id));
    }

    #[Test]
    #[DataProvider('provideConfigsWithFlatteners')]
    public function itGetsFlattener(
        string $expected,
        array $config,
        string $id,
    ): void {
        self::assertSame($expected, (new OperationManager($config))->flattener($id));
    }

    /**
     * @return \Generator<array{
     *     0: bool,
     *     1: array<string, mixed>,
     *     2: string,
     *  }>
     */
    public static function provideConfigsWithOperations(): \Generator
    {
        yield 'config empty' => [false, [], 'find-pet'];
        yield 'config without operation' => [
            false,
            ['operationMap' => [
                'delete-pet' => [],
                'list-pets' => [],
                'find-pet' => [],
            ]],
            'update-pet',
        ];
        yield 'config with operation' => [
            true,
            ['operationMap' => [
                'delete-pet' => [],
                'list-pets' => [],
                'find-pet' => [],
            ]],
            'find-pet',
        ];
    }

    /**
     * @return \Generator<array{
     *     0: class-string,
     *     1: array<string, mixed>,
     *     2: string,
     *  }>
     */
    public static function provideConfigsWithDtos(): \Generator
    {
        yield 'config without operation, uses default' => [
            '\\MyLibrary\\DefaultDto',
            ['default' => ['dto' => '\\MyLibrary\\DefaultDto']],
            'find-pet',
        ];
        yield 'config with operation, without dto, uses default' => [
            '\\MyLibrary\\DefaultDto',
            [
                'default' => ['dto' => '\\MyLibrary\\DefaultDto'],
                'operationMap' => [
                    'find-pet' => []
                ],
            ],
            'find-pet',
        ];
        yield 'config with operation, with dto, uses dto' => [
            '\\MyLibrary\\MyDto',
            [
                'default' => ['dto' => '\\MyLibrary\\DefaultDto'],
                'operationMap' => [
                    'find-pet' => ['dto' => '\\MyLibrary\\MyDto']
                ],
            ],
            'find-pet',
        ];
    }

    /**
     * @return \Generator<array{
     *     0: bool,
     *     1: array<string, mixed>,
     *     2: string,
     *  }>
     */
    public static function provideConfigsUsingFlatteners(): \Generator
    {
        yield 'unspecified operation & default: use global' => [
            true,
            ['default' => [
                'dto' => '\\MyLibrary\\DefaultDto',
            ]],
            'find-pet',
        ];
        yield 'unspecified operation & default: use default' => [
            false,
            ['default' => [
                'dto' => '\\MyLibrary\\DefaultDto',
                'flattener' => Flattener::class,
                'useFlattener' => false,
            ]],
            'find-pet',
        ];
        yield 'empty operation: false uses global flattener' => [
            true,
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                ],
                'operationMap' => [
                    'find-pet' => [],
                ],
            ],
            'find-pet',
        ];
        yield 'empty operation: fall back to default usesFlattener' => [
            true,
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                ],
                'operationMap' => [
                    'find-pet' => [],
                ],
            ],
            'find-pet',
        ];

        yield 'unspecified operation: fallback to specified default' => [
            false,
            ['default' => [
                'dto' => '\\MyLibrary\\DefaultDto',
                'flattener' => Flattener::class,
                'useFlattener' => false,
            ]],
            'find-pet',
        ];

        yield 'operation using default dto, uses default useFlattener' => [
            true,
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                    'flattener' => Flattener::class,
                    'useFlattener' => true,
                ],
                'operationMap' => [
                    'find-pet' => [
                        'useFlattener' => false,
                    ],
                ],
            ],
            'find-pet',
        ];

        yield 'operation specifies dto: fallsback to global' => [
            false,
            [
                'useFlattener' => false,
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                    'flattener' => Flattener::class,
                    'useFlattener' => true,
                ],
                'operationMap' => [
                    'find-pet' => [
                        'dto' => '\\MyLibrary\\MyDto',
                    ],
                ],
            ],
            'find-pet',
        ];
    }

    /**
     * @return \Generator<array{
     *     0: class-string,
     *     1: array<string, mixed>,
     *     2: string,
     *  }>
     */
    public static function provideConfigsWithFlatteners(): \Generator
    {
        yield 'config without operation, uses default' => [
            Flattener::class,
            ['default' => [
                'dto' => '\\MyLibrary\\DefaultDto',
                'flattener' => Flattener::class,
            ]],
            'find-pet',
        ];
        yield 'config with operation, without flattener, uses default' => [
            Flattener::class,
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                    'flattener' => Flattener::class,
                ],
                'operationMap' => [
                    'find-pet' => [],
                ],
            ],
            'find-pet',
        ];
        yield 'config with operation, with flattener, without dto, uses default' => [
            Flattener::class,
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                    'flattener' => Flattener::class,
                ],
                'operationMap' => [
                    'find-pet' => ['flattener' => '\\MyLibrary\\MyFlattener']
                ],
            ],
            'find-pet',
        ];

        yield 'config with operation, with flattener, with dto, uses flattener' => [
            '\\MyLibrary\\MyFlattener',
            [
                'default' => [
                    'dto' => '\\MyLibrary\\DefaultDto',
                    'flattener' => Flattener::class,
                ],
                'operationMap' => [
                    'find-pet' => [
                        'dto' => '\\MyLibrary\\MyDto',
                        'flattener' => '\\MyLibrary\\MyFlattener'
                    ]
                ],
            ],
            'find-pet',
        ];
    }
}
