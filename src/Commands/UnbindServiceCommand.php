<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class UnbindServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-service
                            {name : Service name, supports subdirectory e.g. Pay/Transaction}';

    protected $description = 'Remove only the Service binding for the given model from AppServiceProvider';

    public function handle(): int
    {
        $ctx = NameHelper::buildContext($this->argument('name'));

        $this->output("👉 Remove Binding from AppServiceProvider", 'info');
        BindHelper::removeServiceBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        $this->output("✅ Unbound Service for {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
