<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use CuongNX\RepoServiceGenerator\Traits\StubTrait;
use Illuminate\Support\Str;

class MakeStructureCommand extends Command
{
    use StubTrait, ConsoleOutputTrait;

    protected $signature = 'cuongnx:make-struct 
                            {name : The name of the structure - Model Name} 
                            {--f : Overwrite existing files} {--force : Overwrite existing files}
                            {--m : Create model with type, default is Eloquent} {--model : Create model with type, default is Eloquent}
                            {--t=d : Model type (d=Eloquent, m=Mongodb)} {--type=d : Model type (d=Eloquent, m=Mongodb)}
                            {--no-bind : Do not auto-bind into AppServiceProvider}';

    protected $description = 'Generate Repository, Service and bind them into AppServiceProvider';

    public function handle(): void
    {
        $this->initFilesystem();

        $model = Str::studly($this->argument('name'));
        $force = $this->option('f') || $this->option('force');
        $bind  = !$this->option('no-bind');
        $createModel = $this->option('m') || $this->option('model');
        $modelType = $this->option('t') ?? $this->option('type');

        if ($createModel) {
            $this->createModel($model, $modelType, $force);
        }

        $this->generateRepository($model, $force);
        $this->generateService($model, $force);

        if ($bind) {
            $this->output("👉 Binding into AppServiceProvider", 'info');
            BindHelper::bindModel($model, $this->logCallback());
        }

        $this->output("✅ Structure for {$model} created successfully.", 'info');
    }

    protected function generateRepository(string $model, bool $force): void
    {
        $stubInterfacePath = 'repo-service/repository-interface.stub';
        $stubImplPath      = 'repo-service/repository.stub';

        $interfacePath = app_path("Repositories/Contracts/{$model}RepositoryInterface.php");
        $implPath      = app_path("Repositories/Eloquent/{$model}Repository.php");

        $replacements = ['{{model}}' => $model];

        $this->output("👉 Generating Repository for {$model}", 'info');
        $this->generateFileFromStub($stubInterfacePath, $interfacePath, $replacements, $force, $this->logCallback());
        $this->generateFileFromStub($stubImplPath, $implPath, $replacements, $force, $this->logCallback());
    }

    protected function generateService(string $model, bool $force): void
    {

        $stubInterfacePath = 'repo-service/service-interface.stub';
        $stubImplPath      = 'repo-service/service.stub';

        $interfacePath = app_path("Services/Contracts/{$model}ServiceInterface.php");
        $implPath      = app_path("Services/{$model}Service.php");

        $replacements = ['{{model}}' => $model];

        $this->output("👉 Generating Service for {$model}", 'info');
        $this->generateFileFromStub($stubInterfacePath, $interfacePath, $replacements, $force, $this->logCallback());
        $this->generateFileFromStub($stubImplPath, $implPath, $replacements, $force, $this->logCallback());
    }

    protected function createModel(string $model, string $modelType, bool $force): void
    {
        $this->initFilesystem();

        $modelPath = app_path("Models/{$model}.php");

        $this->output("👉 Generating Model for {$model}", 'info');
        if ($this->files->exists($modelPath) && !$force) {
            $this->output("❌ Model {$modelPath} already exists. Use --f to overwrite!", 'warn');
            return;
        }

        $this->makeDirectory($modelPath);

        $stub = match ($modelType) {
            'm' => $this->getStub('model/model.mongo.stub'),
            default => $this->getStub('model/model.default.stub'),
        };

        $collection = $modelType === 'm' ? Str::snake(Str::pluralStudly($model)) : '';

        $replacements = [
            '{{model}}' => $model,
            '{{collection}}' => $collection,
        ];

        $stubContent = str_replace(array_keys($replacements), array_values($replacements), $stub);

        $this->files->put($modelPath, $stubContent);

        $dbType = $modelType === 'm' ? 'mongodb' : 'eloquent';
        $this->output("☑️  Model {$modelPath} is created with '{$dbType}'");
    }

}
