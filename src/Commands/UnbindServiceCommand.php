<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnbindServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:unbind-service 
                            {name : The model name to unbind Service only (e.g. User)} ';

    protected $description = 'Remove only the Service for the given model to AppServiceProvider';

    public function handle(): int
    {
        $model = Str::studly($this->argument('name'));

        $this->output("👉 Remove Binding from AppServiceProvider", 'info');
        BindHelper::removeServiceBinding($model, $this->logCallback());
        $this->output("✅ Unbound Service for model {$model} successfully.", 'info');
        return Command::SUCCESS;
    }
}
