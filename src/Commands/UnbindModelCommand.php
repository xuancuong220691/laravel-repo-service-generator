<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class UnbindModelCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-model
                            {model : Model name, supports subdirectory e.g. Pay/Transaction}
                            {--only= : Unbind only [repo|service]}';

    protected $description = 'Remove repository/service binding for a model from AppServiceProvider';

    public function handle(): int
    {
        $ctx  = NameHelper::buildContext($this->argument('model'));
        $only = $this->option('only');

        $this->output("👉 Removing Binding from AppServiceProvider", 'info');
        match ($only) {
            'repo'    => BindHelper::removeRepositoryBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            'service' => BindHelper::removeServiceBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            null      => BindHelper::removeModelBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']),
            default   => $this->output("❌ Option --only must be [repo|service].", 'error'),
        };
        $this->output("✅ Unbound {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
