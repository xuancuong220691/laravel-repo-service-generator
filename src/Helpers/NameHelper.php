<?php

namespace CuongNX\RepoServiceGenerator\Helpers;

use Illuminate\Support\Str;

class NameHelper
{
    /**
     * Parse input like 'Transaction', 'Pay/Transaction', or 'Pay/Gateway/Transaction'.
     *
     * @return array{
     *     model: string,
     *     subDir: string,
     *     subNamespace: string,
     *     displayName: string
     * }
     */
    public static function parse(string $input): array
    {
        $input = str_replace('\\', '/', trim($input, '/\\'));

        $parts = array_map(fn($p) => Str::studly($p), explode('/', $input));

        $model        = array_pop($parts);
        $subDir       = implode('/', $parts);
        $subNamespace = implode('\\', $parts);

        return [
            'model'        => $model,
            'subDir'       => $subDir,
            'subNamespace' => $subNamespace,
            'displayName'  => $subDir ? "{$subDir}/{$model}" : $model,
        ];
    }

    /**
     * Append subNamespace to a base namespace.
     * buildNamespace('App\Services', 'Pay\Gateway') → 'App\Services\Pay\Gateway'
     * buildNamespace('App\Services', '')            → 'App\Services'
     */
    public static function buildNamespace(string $base, string $subNamespace): string
    {
        return $subNamespace ? "{$base}\\{$subNamespace}" : $base;
    }

    /**
     * Build a relative file path with an optional subdirectory.
     * buildPath('Services', 'Pay/Gateway', 'TransactionService.php') → 'Services/Pay/Gateway/TransactionService.php'
     * buildPath('Services', '', 'TransactionService.php')            → 'Services/TransactionService.php'
     */
    public static function buildPath(string $baseDir, string $subDir, string $filename): string
    {
        return $subDir ? "{$baseDir}/{$subDir}/{$filename}" : "{$baseDir}/{$filename}";
    }

    /**
     * Build all namespace and path info needed for repo-service generation.
     */
    public static function buildContext(string $input): array
    {
        $parsed = self::parse($input);

        $model        = $parsed['model'];
        $subDir       = $parsed['subDir'];
        $subNamespace = $parsed['subNamespace'];

        $repoContractsNs  = self::buildNamespace('App\\Repositories\\Contracts', $subNamespace);
        $repoImplNs       = self::buildNamespace('App\\Repositories\\Eloquent', $subNamespace);
        $serviceContractsNs = self::buildNamespace('App\\Services\\Contracts', $subNamespace);
        $serviceImplNs    = self::buildNamespace('App\\Services', $subNamespace);
        $modelNs          = self::buildNamespace('App\\Models', $subNamespace);

        return [
            'model'                 => $model,
            'subDir'                => $subDir,
            'subNamespace'          => $subNamespace,
            'displayName'           => $parsed['displayName'],
            'repoContractsNs'       => $repoContractsNs,
            'repoImplNs'            => $repoImplNs,
            'serviceContractsNs'    => $serviceContractsNs,
            'serviceImplNs'         => $serviceImplNs,
            'modelFqn'              => "{$modelNs}\\{$model}",

            // File paths (relative to app/)
            'repoInterfacePath'     => self::buildPath('Repositories/Contracts', $subDir, "{$model}RepositoryInterface.php"),
            'repoImplPath'          => self::buildPath('Repositories/Eloquent',  $subDir, "{$model}Repository.php"),
            'serviceInterfacePath'  => self::buildPath('Services/Contracts',     $subDir, "{$model}ServiceInterface.php"),
            'serviceImplPath'       => self::buildPath('Services',               $subDir, "{$model}Service.php"),
            'modelPath'             => self::buildPath('Models',                 $subDir, "{$model}.php"),
        ];
    }
}
