<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class BindServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-service
                            {name : Service name, supports subdirectory e.g. Pay/Transaction}';

    protected $description = 'Bind only the Service for the given model to AppServiceProvider';

    public function handle(): int
    {
        $ctx = NameHelper::buildContext($this->argument('name'));

        $this->output("👉 Binding into AppServiceProvider", 'info');
        BindHelper::bindService($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        $this->output("✅ Bind Service for {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
