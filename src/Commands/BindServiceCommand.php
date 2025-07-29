<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BindServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-service 
                            {name : The model name to bind Service only (e.g. User)} ';

    protected $description = 'Bind only the Service for the given model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('name'));

        $this->output("👉 Binding into AppServiceProvider", 'info');
        BindHelper::bindService($model, $this->logCallback());
        $this->output("✅ Bind Service for model {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
