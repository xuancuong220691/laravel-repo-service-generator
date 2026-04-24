<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;

class UnbindRepoCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-repo
                            {name : Model name, supports subdirectory e.g. Pay/Transaction}';

    protected $description = 'Remove only the Repository binding for the given model from AppServiceProvider';

    public function handle(): int
    {
        $ctx = NameHelper::buildContext($this->argument('name'));

        $this->output("👉 Remove from AppServiceProvider", 'info');
        BindHelper::removeRepositoryBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        $this->output("✅ Unbound Repository for {$ctx['displayName']} successfully.", 'info');
        return Command::SUCCESS;
    }
}
