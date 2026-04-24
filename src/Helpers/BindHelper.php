<?php

namespace CuongNX\RepoServiceGenerator\Helpers;

use Illuminate\Support\Facades\File;

class BindHelper
{
    protected static function getProviderPath(): string
    {
        return app_path('Providers/AppServiceProvider.php');
    }

    protected static function out(?callable $outputHandler, string $message, string $type = 'line'): void
    {
        if ($outputHandler) {
            $outputHandler($message, $type);
        }
    }

    /**
     * Build fully-qualified class names for repo interface and implementation.
     * subNamespace '' → App\Repositories\Contracts\UserRepositoryInterface
     * subNamespace 'Pay' → App\Repositories\Contracts\Pay\UserRepositoryInterface
     */
    protected static function repoFqn(string $model, string $subNamespace): array
    {
        $ns  = $subNamespace ? "App\\Repositories\\Contracts\\{$subNamespace}" : 'App\\Repositories\\Contracts';
        $imp = $subNamespace ? "App\\Repositories\\Eloquent\\{$subNamespace}" : 'App\\Repositories\\Eloquent';

        return [
            'interface' => "{$ns}\\{$model}RepositoryInterface",
            'impl'      => "{$imp}\\{$model}Repository",
        ];
    }

    /**
     * Build fully-qualified class names for service interface and implementation.
     */
    protected static function serviceFqn(string $model, string $subNamespace): array
    {
        $ns  = $subNamespace ? "App\\Services\\Contracts\\{$subNamespace}" : 'App\\Services\\Contracts';
        $imp = $subNamespace ? "App\\Services\\{$subNamespace}" : 'App\\Services';

        return [
            'interface' => "{$ns}\\{$model}ServiceInterface",
            'impl'      => "{$imp}\\{$model}Service",
        ];
    }

    protected static function insertBinding(string $binding, string $identifier, ?callable $outputHandler = null): bool
    {
        $provider = self::getProviderPath();

        if (!File::exists($provider)) {
            self::out($outputHandler, "⚠️  AppServiceProvider not found.", 'error');
            return false;
        }

        $content = File::get($provider);

        if (str_contains($content, $identifier)) {
            self::out($outputHandler, "🔁  Binding already exists: {$identifier}", 'warn');
            return false;
        }

        $pattern = '/public function register\s*\([^\)]*\)\s*(?::\s*void\s*)?\{/';
        if (!preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            self::out($outputHandler, "⚠️ Can't find register() in AppServiceProvider.", 'error');
            return false;
        }

        $insertAt   = $matches[0][1] + strlen($matches[0][0]);
        $newContent = substr($content, 0, $insertAt) . "\n" . $binding . substr($content, $insertAt);

        File::put($provider, $newContent);
        self::out($outputHandler, "🔗  Binding added: {$identifier}");

        return true;
    }

    public static function bindRepository(string $model, ?callable $outputHandler = null, string $subNamespace = ''): bool
    {
        $fqn = self::repoFqn($model, $subNamespace);

        $binding = <<<PHP
                // Binding for {$fqn['interface']}
                \$this->app->bind(
                    \\{$fqn['interface']}::class,
                    \\{$fqn['impl']}::class
                );

        PHP;

        return self::insertBinding($binding, $fqn['interface'], $outputHandler);
    }

    public static function bindService(string $model, ?callable $outputHandler = null, string $subNamespace = ''): bool
    {
        $fqn = self::serviceFqn($model, $subNamespace);

        $binding = <<<PHP
                // Binding for {$fqn['interface']}
                \$this->app->bind(
                    \\{$fqn['interface']}::class,
                    \\{$fqn['impl']}::class
                );

        PHP;

        return self::insertBinding($binding, $fqn['interface'], $outputHandler);
    }

    public static function bindModel(string $model, ?callable $outputHandler = null, string $subNamespace = ''): void
    {
        self::bindRepository($model, $outputHandler, $subNamespace);
        self::bindService($model, $outputHandler, $subNamespace);
    }

    protected static function removeBinding(string $identifier, ?callable $outputHandler = null): bool
    {
        $provider = self::getProviderPath();

        if (!File::exists($provider)) {
            self::out($outputHandler, "⚠️ AppServiceProvider not found.", 'error');
            return false;
        }

        $lines      = file($provider);
        $newLines   = [];
        $removing   = false;
        $removed    = false;
        $depth      = 0;
        $skipBlank  = false;

        foreach ($lines as $line) {
            if (!$removing && str_contains($line, '// Binding for') && str_contains($line, $identifier)) {
                $removing  = true;
                $removed   = true;
                $depth     = 0;
                continue;
            }

            if ($removing) {
                $depth += substr_count($line, '(') - substr_count($line, ')');
                if ($depth <= 0 && str_contains($line, ');')) {
                    $removing  = false;
                    $skipBlank = true;
                }
                continue;
            }

            if ($skipBlank) {
                $skipBlank = false;
                if (trim($line) === '') {
                    continue;
                }
            }

            $newLines[] = $line;
        }

        if ($removed) {
            File::put($provider, implode('', $newLines));
            self::out($outputHandler, "🧹 Binding removed: {$identifier}");
        } else {
            self::out($outputHandler, "⚠️ Binding not found: {$identifier}", 'warn');
        }

        return $removed;
    }

    public static function removeRepositoryBinding(string $model, ?callable $outputHandler = null, string $subNamespace = ''): bool
    {
        $fqn = self::repoFqn($model, $subNamespace);
        return self::removeBinding($fqn['interface'], $outputHandler);
    }

    public static function removeServiceBinding(string $model, ?callable $outputHandler = null, string $subNamespace = ''): bool
    {
        $fqn = self::serviceFqn($model, $subNamespace);
        return self::removeBinding($fqn['interface'], $outputHandler);
    }

    public static function removeModelBinding(string $model, ?callable $outputHandler = null, string $subNamespace = ''): void
    {
        self::removeRepositoryBinding($model, $outputHandler, $subNamespace);
        self::removeServiceBinding($model, $outputHandler, $subNamespace);
    }
}
