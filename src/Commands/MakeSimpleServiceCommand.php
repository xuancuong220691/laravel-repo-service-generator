<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use CuongNX\RepoServiceGenerator\Traits\StubTrait;
use Illuminate\Support\Str;

class MakeSimpleServiceCommand extends Command
{
    use StubTrait, ConsoleOutputTrait;

    protected $signature = 'cuongnx:make-service 
                            {name : The name of the service class (no "Service" suffix needed)} 
                            {--f : Overwrite if exists} {--force : Overwrite if exists} 
                            {--no-bind : Do not bind in AppServiceProvider}';

    protected $description = 'Create a simple Service class and optionally bind it to AppServiceProvider.';

    public function handle(): void
    {
        $this->initFilesystem();

        $name = Str::studly($this->argument('name'));
        $force = $this->option('f') || $this->option('force');
        $bind  = !$this->option('no-bind');
      
        $this->generateService($name, $force);

        if ($bind) {
            $this->output("👉 Binding into AppServiceProvider", 'info');
            BindHelper::bindService($name, $this->logCallback());
        }

        $this->output("✅ Created Service {$name} successfully.", 'info');
    }

    protected function generateService(string $name, bool $force): void
    {

        $stubInterfacePath = 'simple-service/service-interface.stub';
        $stubImplPath      = 'simple-service/service.stub';

        $interfacePath = app_path("Services/Contracts/{$name}ServiceInterface.php");
        $implPath      = app_path("Services/{$name}Service.php");

        $replacements = ['{{name}}' => $name];

        $this->output("👉 Generating Service for {$name}", 'info');
        $this->generateFileFromStub($stubInterfacePath, $interfacePath, $replacements, $force, $this->logCallback());
        $this->generateFileFromStub($stubImplPath, $implPath, $replacements, $force, $this->logCallback());
    }

}
