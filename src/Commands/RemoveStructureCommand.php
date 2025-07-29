<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RemoveStructureCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:remove-struct 
                            {name : The structure name (Model name)} 
                            {--m : Also delete model}
                            {--model : Also delete model}
                            {--no-unbind : Do not remove binding in AppServiceProvider}';

    protected $description = 'Remove Repository, Service, and optionally Model.';

    public function handle(): void
    {
        $model = Str::studly($this->argument('name'));
        $deleteModel = $this->option('model') || $this->option('m');
        $unbind = !$this->option('no-unbind');

        $files = [
            app_path("Repositories/Contracts/{$model}RepositoryInterface.php"),
            app_path("Repositories/Eloquent/{$model}Repository.php"),
            app_path("Services/Contracts/{$model}ServiceInterface.php"),
            app_path("Services/{$model}Service.php"),
        ];

        if ($deleteModel) {
            $files[] = app_path("Models/{$model}.php");
        }

        $this->output("👉 Removing structure for {$model}", 'info');
        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->output("🗑️  Deleted: {$file}");
            } else {
                $this->output("⚠️  Not found: {$file}", 'warn');
            }
        }

        if ($unbind) {
            $this->output("🚫 Removing bindings from AppServiceProvider...", 'info');
            BindHelper::removeModelBinding($model, $this->logCallback());
        }

        $this->output("✅ Removed structure for {$model}" . ($deleteModel ? " (with model)" : ""), 'info');
    }
}
