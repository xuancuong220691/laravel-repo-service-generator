<?php

namespace CuongNX\RepoServiceGenerator\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
{
    $this->commands([
        \CuongNX\RepoServiceGenerator\Commands\MakeBaseCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\MakeStructureCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\MakeSimpleServiceCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\RemoveStructureCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\RemoveSimpleServiceCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\BindModelCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\BindRepoCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\BindServiceCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\UnbindModelCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\UnbindRepoCommand::class,
        \CuongNX\RepoServiceGenerator\Commands\UnbindServiceCommand::class,
    ]);
}

}
