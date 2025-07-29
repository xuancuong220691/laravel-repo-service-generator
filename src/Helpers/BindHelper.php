<?php

namespace CuongNX\RepoServiceGenerator\Helpers;

use Illuminate\Support\Facades\File;

class BindHelper
{
    protected static function getProviderPath(): string
    {
        return app_path('Providers/AppServiceProvider.php');
    }

    protected static function insertBinding(string $binding, string $identifier, ?callable $outputHandler = null): bool
    {
        $provider = self::getProviderPath();

        if (!File::exists($provider)) {
            $outputHandler("⚠️  AppServiceProvider not found.", 'error');
            return false;
        }

        $content = File::get($provider);

        if (str_contains($content, $identifier)) {
            $outputHandler("🔁  Binding for {$identifier} already exists.", 'warn');
            return false;
        }

        $pattern = '/public function register\s*\([^\)]*\)\s*:\s*void\s*\{/';
        if (!preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $outputHandler("⚠️ Can't find register() in AppServiceProvider.", 'error');
            return false;
        }

        $insertAt = $matches[0][1] + strlen($matches[0][0]);
        $newContent = substr($content, 0, $insertAt) . "\n" . $binding . substr($content, $insertAt);

        File::put($provider, $newContent);
        $outputHandler("🔗  Binding for {$identifier} added to AppServiceProvider.");

        return true;
    }

    public static function bindRepository(string $model, ?callable $outputHandler = null): bool
    {
        $binding = <<<PHP
                // Binding for {$model}Repository
                \$this->app->bind(
                    \\App\\Repositories\\Contracts\\{$model}RepositoryInterface::class,
                    \\App\\Repositories\\Eloquent\\{$model}Repository::class
                );

        PHP;

        return self::insertBinding($binding, "{$model}RepositoryInterface", $outputHandler);
    }

    public static function bindService(string $model, ?callable $outputHandler = null): bool
    {
        $binding = <<<PHP
                // Binding for {$model}Service
                \$this->app->bind(
                    \\App\\Services\\Contracts\\{$model}ServiceInterface::class,
                    \\App\\Services\\{$model}Service::class
                );

        PHP;

        return self::insertBinding($binding, "{$model}ServiceInterface", $outputHandler);
    }

    public static function bindModel(string $model, ?callable $outputHandler = null): void
    {
        self::bindRepository($model, $outputHandler);
        self::bindService($model, $outputHandler);
    }

    public static function removeBinding(string $identifier, ?callable $outputHandler = null): bool
    {
        $provider = self::getProviderPath();

        if (!File::exists($provider)) {
            $outputHandler("⚠️ AppServiceProvider not found.", 'error');
            return false;
        }

        $lines = file($provider);
        $newLines = [];
        $removing = false;
        $removed = false;

        foreach ($lines as $line) {
            if (!$removing && str_contains($line, '// Binding for') && str_contains($line, $identifier)) {
                $removing = true;
                $removed = true;
                continue;
            }

            if ($removing) {
                if (str_contains($line, ');')) {
                    $removing = false;
                }
                continue;
            }

            $newLines[] = $line;
        }

        if ($removed) {
            File::put($provider, implode('', $newLines));
            $outputHandler("🧹 Binding for {$identifier} removed from AppServiceProvider.");
        } else {
            $outputHandler("⚠️ Binding for {$identifier} not found in AppServiceProvider.", 'warn');
        }

        return $removed;
    }





    public static function removeRepositoryBinding(string $model, ?callable $outputHandler = null): bool
    {
        return self::removeBinding("{$model}Repository", $outputHandler);
    }

    public static function removeServiceBinding(string $model, ?callable $outputHandler = null): bool
    {
        return self::removeBinding("{$model}Service", $outputHandler);
    }

    public static function removeModelBinding(string $model, ?callable $outputHandler = null): void
    {
        self::removeRepositoryBinding($model, $outputHandler);
        self::removeServiceBinding($model, $outputHandler);
    }
}
