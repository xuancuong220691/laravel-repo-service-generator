<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class BindModelCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-model
                            {model : Model name, supports subdirectory e.g. Pay/Transaction}
                            {--only= : Bind only [repo|service]}';

    protected $description = 'Bind repository/service interface for a model to AppServiceProvider';

    public function handle(): int
    {
        $ctx  = NameHelper::buildContext($this->argument('model'));
        $only = $this->option('only');

        $this->output("👉 Binding into AppServiceProvider", 'info');
        match ($only) {
            'repo'    => BindHelper::bindRepository($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            'service' => BindHelper::bindService($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            null      => BindHelper::bindModel($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            default   => $this->output("❌ Option --only must be [repo|service].", 'error'),
        };
        $this->output("✅ Bind {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
