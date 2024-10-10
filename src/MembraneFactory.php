<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Membrane\Membrane;
use Membrane\OpenAPIRouter\Router;

final class MembraneFactory
{
    public function __invoke(array $config): Membrane
    {
        $builders = [];
        if (isset($config['routes_file'])) {
            $router = new Router(include $config['routes_file']);
        }

        if (isset($router) && isset($config['cached_builders']) && is_array($config['cached_builders'])) {
            foreach ($config['cached_builders'] as $builder) {
                $builders[] = new $builder($router);
            }
        }

        if (isset($config['disable_default_builders']) && $config['disable_default_builders'] === true) {
            return Membrane::withoutDefaults(...$builders);
        }

        return new Membrane(...$builders);
    }
}