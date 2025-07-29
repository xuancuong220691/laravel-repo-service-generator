<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;

class RemoveSimpleServiceCommand extends Command
{
    use ConsoleOutputTrait;

    protected $signature = 'cuongnx:remove-service 
                            {name : The service class name (no "Service" suffix)} 
                            {--no-unbind : Do not remove binding in AppServiceProvider}';

    protected $description = 'Remove a simple Service and optionally its binding in AppServiceProvider.';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $unbind = !$this->option('no-unbind');

        $files = [
            app_path("Services/Contracts/{$name}ServiceInterface.php"),
            app_path("Services/{$name}Service.php"),
        ];

        $this->output("👉 Removing Service {$name}", 'info');
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
            BindHelper::removeServiceBinding($name, $this->logCallback());
        }

        $this->info("✅ Service {$name} removed.");
    }
}
