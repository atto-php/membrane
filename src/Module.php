<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Atto\Framework\Module\ModuleInterface;
use Atto\Framework\Response\Builder;
use Atto\Framework\Response\Errors\ErrorHandler;
use Atto\Membrane\Application\MembraneOpenApi;
use Membrane\Membrane;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;

final class Module implements ModuleInterface
{
    public function getServices(): array
    {
        return [
            MembraneOpenApi::class => [
                'args' => [
                    ContainerInterface::class,
                    OperationManager::class,
                    Builder::class,
                    RequestParser::class
                ]
            ],
            OperationManager::class => [
                'args' => [
                    'config.membrane'
                ]
            ],
            RequestParser::class => [
                'args' => [
                    ServerRequestCreator::class,
                    OperationManager::class,
                    Membrane::class,
                    'config.membrane.openAPISpec'
                ]
            ],
            ServerRequestCreator::class => [
                'args' => [
                    Psr17Factory::class,
                    Psr17Factory::class,
                    Psr17Factory::class,
                    Psr17Factory::class,
                ]
            ],
            Membrane::class => [],
            RequestProblemHandler::class => [
                'tags' => [
                    ErrorHandler::class
                ]
            ]
        ];
    }

    public function getConfig(): array
    {
        return [];
    }

}