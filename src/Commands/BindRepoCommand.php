<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class BindRepoCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:bind-repo
                            {name : Model name, supports subdirectory e.g. Pay/Transaction}';

    protected $description = 'Bind only the Repository for the given model to AppServiceProvider';

    public function handle(): int
    {
        $ctx = NameHelper::buildContext($this->argument('name'));

        $this->output("👉 Binding into AppServiceProvider", 'info');
        BindHelper::bindRepository($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        $this->output("✅ Bind Repository for {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
