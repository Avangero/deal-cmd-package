<?php

declare(strict_types=1);

namespace Illuminate\Support {
    if (! class_exists(ServiceProvider::class)) {
        abstract class ServiceProvider
        {
            /** @var mixed */
            protected $app;

            /** @return string[] */
            public function provides(): array
            {
                return [];
            }

            /**
             * @param  array<string, string>  $paths
             */
            public function publishes(array $paths, ?string $group = null): void
            {
                // no-op for static analysis
            }
        }
    }
}

namespace {
    if (! function_exists('config_path')) {
        function config_path(string $path = ''): string
        {
            return __DIR__ . '/../config' . ($path !== '' ? '/' . ltrim($path, '/') : '');
        }
    }
}
