<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnbindRepoCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-repo 
                            {name : The model name to unbind Repository only (e.g. User)} ';

    protected $description = 'Remove only the Repository for the given model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('name'));

        $this->output("👉 Remove from AppServiceProvider", 'info');
        BindHelper::removeRepositoryBinding($model, $this->logCallback());
        $this->output("✅ Unbound Repository for model {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
