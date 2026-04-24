<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveStructureCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:remove-struct
                            {name : Structure name, supports subdirectory e.g. Pay/Transaction}
                            {--m : Also delete model} {--model : Also delete model}
                            {--no-unbind : Do not remove binding in AppServiceProvider}';

    protected $description = 'Remove Repository, Service, and optionally Model.';

    public function handle(): void
    {
        $ctx         = NameHelper::buildContext($this->argument('name'));
        $deleteModel = $this->option('model') || $this->option('m');
        $unbind      = !$this->option('no-unbind');

        $files = [
            app_path($ctx['repoInterfacePath']),
            app_path($ctx['repoImplPath']),
            app_path($ctx['serviceInterfacePath']),
            app_path($ctx['serviceImplPath']),
        ];

        if ($deleteModel) {
            $files[] = app_path($ctx['modelPath']);
        }

        $this->output("👉 Removing structure for {$ctx['displayName']}", 'info');
        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->output("🗑️  Deleted: {$file}");
            } else {
                $this->output("⚠️  Not found: {$file}", 'warn');
            }
        }

        if ($unbind) {
            $this->output("🚫 Removing bindings from AppServiceProvider...", 'info');
            BindHelper::removeModelBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        }

        $this->output("✅ Removed structure for {$ctx['displayName']}" . ($deleteModel ? " (with model)" : ""), 'info');
    }
}
