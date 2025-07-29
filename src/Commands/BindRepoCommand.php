<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BindRepoCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-repo 
                            {name : The model name to bind Repository only (e.g. User)} ';

    protected $description = 'Bind only the Repository for the given model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('name'));

        $this->output("👉 Binding into AppServiceProvider", 'info');
        BindHelper::bindRepository($model, $this->logCallback());
        $this->output("✅ Bind Repository for model {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
