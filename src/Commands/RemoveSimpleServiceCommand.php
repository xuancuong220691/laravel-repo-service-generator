<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;

class RemoveSimpleServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:remove-service
                            {name : Service name, supports subdirectory e.g. Pay/Transaction}
                            {--no-unbind : Do not remove binding in AppServiceProvider}';

    protected $description = 'Remove a simple Service and optionally its binding in AppServiceProvider.';

    public function handle(): void
    {
        $ctx    = NameHelper::buildContext($this->argument('name'));
        $unbind = !$this->option('no-unbind');

        $files = [
            app_path(NameHelper::buildPath('Services/Contracts', $ctx['subDir'], "{$ctx['model']}ServiceInterface.php")),
            app_path(NameHelper::buildPath('Services',           $ctx['subDir'], "{$ctx['model']}Service.php")),
        ];

        $this->output("👉 Removing Service {$ctx['displayName']}", 'info');
        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->output("🗑️  Deleted: {$file}");
            } else {
                $this->output("⚠️  Not found: {$file}", 'warn');
            }
        }

        if ($unbind) {
            $this->output("🚫 Removing service bindings from AppServiceProvider...", 'info');
            BindHelper::removeServiceBinding($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        }

        $this->output("✅ Service {$ctx['displayName']} removed.", 'info');
    }
}
