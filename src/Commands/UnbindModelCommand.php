<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnbindModelCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-model 
                            {model : The model name to unbind Repository and Service (e.g. User)} 
                            {--only= : Bind only [repo|service]}';

    protected $description = 'Remove repository/service interface for a model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('model'));
        $only  = $this->option('only');

        $this->output("👉 Removing Binding from AppServiceProvider", 'info');
        match ($only) {
            'repo'    => BindHelper::removeRepositoryBinding($model, $this->logCallback()),
            'service' => BindHelper::removeServiceBinding($model, $this->logCallback()),
            null      => BindHelper::removeModelBinding($model, $this->logCallback()),
            default   => $this->output("❌ Option --only must be [repo|service].", 'error'),
        };
        $this->output("✅ Unbound {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
