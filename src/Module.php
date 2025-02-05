<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Atto\Framework\Module\ModuleInterface;
use Atto\Framework\Response\Builder;
use Atto\Framework\Response\Errors\ErrorHandler;
use Atto\Membrane\Application\MembraneOpenApi;
use Atto\Psr7\ResponseEmitter;
use Membrane\Membrane;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

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
                    RequestParser::class,
                    ResponseEmitter::class
                ]
            ],
            OperationManager::class => [
                'args' => [
                    'config.membrane'
                ]
            ],
            RequestParser::class => [
                'args' => [
                    ServerRequestInterface::class,
                    OperationManager::class,
                    Membrane::class,
                    'config.membrane.openAPISpec'
                ]
            ],
            Membrane::class => [
                'factory' => new MembraneFactory(),
                'args' => ['config.membrane']
            ],
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