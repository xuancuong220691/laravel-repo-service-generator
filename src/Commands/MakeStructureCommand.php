<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use CuongNX\RepoServiceGenerator\Traits\StubTrait;
use Illuminate\Support\Str;

class MakeStructureCommand extends Command
{
    use StubTrait, ConsoleOutputTrait;

    protected $signature = 'cuongnx:make-struct
                            {name : Model name, supports subdirectory e.g. Pay/Transaction}
                            {--f : Overwrite existing files} {--force : Overwrite existing files}
                            {--m : Create model} {--model : Create model}
                            {--t=d : Model type (d=Eloquent, m=Mongodb)} {--type=d : Model type (d=Eloquent, m=Mongodb)}
                            {--no-bind : Do not auto-bind into AppServiceProvider}';

    protected $description = 'Generate Repository, Service and bind them into AppServiceProvider';

    public function handle(): void
    {
        $this->initFilesystem();

        $ctx         = NameHelper::buildContext($this->argument('name'));
        $force       = $this->option('f') || $this->option('force');
        $bind        = !$this->option('no-bind');
        $createModel = $this->option('m') || $this->option('model');
        $modelType   = $this->option('type') !== 'd' ? $this->option('type') : $this->option('t');

        if ($createModel) {
            $this->createModel($ctx, $modelType, $force);
        }

        $this->generateRepository($ctx, $force);
        $this->generateService($ctx, $force);

        if ($bind) {
            $this->output("👉 Binding into AppServiceProvider", 'info');
            BindHelper::bindModel($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        }

        $this->output("✅ Structure for {$ctx['displayName']} created successfully.", 'info');
    }

    protected function resolveBaseRepositoryClass(): string
    {
        $appBase = app_path('Repositories/Eloquent/BaseRepository.php');

        return $this->files->exists($appBase)
            ? 'App\\Repositories\\Eloquent\\BaseRepository'
            : 'CuongNX\\RepoServiceGenerator\\Base\\BaseRepository';
    }

    protected function resolveBaseRepositoryInterfaceClass(): string
    {
        $appBase = app_path('Repositories/Contracts/BaseRepositoryInterface.php');

        return $this->files->exists($appBase)
            ? 'App\\Repositories\\Contracts\\BaseRepositoryInterface'
            : 'CuongNX\\RepoServiceGenerator\\Base\\Contracts\\BaseRepositoryInterface';
    }

    protected function resolveBaseServiceClass(): string
    {
        $appBase = app_path('Services/BaseService.php');

        return $this->files->exists($appBase)
            ? 'App\\Services\\BaseService'
            : 'CuongNX\\RepoServiceGenerator\\Base\\BaseService';
    }

    protected function resolveBaseServiceInterfaceClass(): string
    {
        $appBase = app_path('Services/Contracts/BaseServiceInterface.php');

        return $this->files->exists($appBase)
            ? 'App\\Services\\Contracts\\BaseServiceInterface'
            : 'CuongNX\\RepoServiceGenerator\\Base\\Contracts\\BaseServiceInterface';
    }

    protected function generateRepository(array $ctx, bool $force): void
    {
        $replacements = [
            '{{model}}'                       => $ctx['model'],
            '{{repoContractsNamespace}}'      => $ctx['repoContractsNs'],
            '{{repoImplNamespace}}'           => $ctx['repoImplNs'],
            '{{modelFqn}}'                    => $ctx['modelFqn'],
            '{{baseRepositoryClass}}'         => 'use ' . $this->resolveBaseRepositoryClass() . ';',
            '{{baseRepositoryInterfaceClass}}' => 'use ' . $this->resolveBaseRepositoryInterfaceClass() . ';',
        ];

        $this->output("👉 Generating Repository for {$ctx['displayName']}", 'info');

        $this->generateFileFromStub(
            'repo-service/repository-interface.stub',
            app_path($ctx['repoInterfacePath']),
            $replacements, $force, $this->logCallback()
        );
        $this->generateFileFromStub(
            'repo-service/repository.stub',
            app_path($ctx['repoImplPath']),
            $replacements, $force, $this->logCallback()
        );
    }

    protected function generateService(array $ctx, bool $force): void
    {
        $replacements = [
            '{{model}}'                      => $ctx['model'],
            '{{repoContractsNamespace}}'     => $ctx['repoContractsNs'],
            '{{serviceContractsNamespace}}'  => $ctx['serviceContractsNs'],
            '{{serviceImplNamespace}}'       => $ctx['serviceImplNs'],
            '{{baseServiceClass}}'           => 'use ' . $this->resolveBaseServiceClass() . ';',
            '{{baseServiceInterfaceClass}}'  => 'use ' . $this->resolveBaseServiceInterfaceClass() . ';',
        ];

        $this->output("👉 Generating Service for {$ctx['displayName']}", 'info');

        $this->generateFileFromStub(
            'repo-service/service-interface.stub',
            app_path($ctx['serviceInterfacePath']),
            $replacements, $force, $this->logCallback()
        );
        $this->generateFileFromStub(
            'repo-service/service.stub',
            app_path($ctx['serviceImplPath']),
            $replacements, $force, $this->logCallback()
        );
    }

    protected function createModel(array $ctx, string $modelType, bool $force): void
    {
        $modelPath = app_path($ctx['modelPath']);

        $this->output("👉 Generating Model for {$ctx['displayName']}", 'info');

        if ($this->files->exists($modelPath) && !$force) {
            $this->output("❌ Model {$modelPath} already exists. Use --f to overwrite!", 'warn');
            return;
        }

        $stub = match ($modelType) {
            'm'     => $this->getStub('model/model.mongo.stub'),
            default => $this->getStub('model/model.default.stub'),
        };

        $collection = $modelType === 'm' ? Str::snake(Str::pluralStudly($ctx['model'])) : '';

        $content = str_replace(
            ['{{model}}', '{{collection}}'],
            [$ctx['model'], $collection],
            $stub
        );

        $this->makeDirectory($modelPath);
        $this->files->put($modelPath, $content);

        $dbType = $modelType === 'm' ? 'mongodb' : 'eloquent';
        $this->output("☑️  Model {$modelPath} is created with '{$dbType}'");
    }
}
