<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BindModelCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-model 
                            {model : The model name to bind Repository and Service (e.g. User)} 
                            {--only= : Bind only [repo|service]}';

    protected $description = 'Bind repository/service interface for a model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('model'));
        $only  = $this->option('only');

        $this->output("👉 Binding into AppServiceProvider", 'info');
        match ($only) {
            'repo'    => BindHelper::bindRepository($model, $this->logCallback()),
            'service' => BindHelper::bindService($model, $this->logCallback()),
            null      => BindHelper::bindModel($model, $this->logCallback()),
            default   => $this->output("❌ Option --only must be [repo|service].", 'error'),
        };
        $this->output("✅ Bind {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
