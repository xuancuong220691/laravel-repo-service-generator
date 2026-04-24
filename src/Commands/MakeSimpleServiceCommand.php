<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use Illuminate\Console\Command;
use CuongNX\RepoServiceGenerator\Helpers\BindHelper;
use CuongNX\RepoServiceGenerator\Helpers\NameHelper;
use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use CuongNX\RepoServiceGenerator\Traits\StubTrait;

class MakeSimpleServiceCommand extends Command
{
    use StubTrait, ConsoleOutputTrait;

    protected $signature = 'cuongnx:make-service
                            {name : Service name, supports subdirectory e.g. Pay/Transaction}
                            {--f : Overwrite if exists} {--force : Overwrite if exists}
                            {--no-bind : Do not bind in AppServiceProvider}';

    protected $description = 'Create a simple Service class and optionally bind it to AppServiceProvider.';

    public function handle(): void
    {
        $this->initFilesystem();

        $ctx   = NameHelper::buildContext($this->argument('name'));
        $force = $this->option('f') || $this->option('force');
        $bind  = !$this->option('no-bind');

        $this->generateService($ctx, $force);

        if ($bind) {
            $this->output("👉 Binding into AppServiceProvider", 'info');
            BindHelper::bindService($ctx['model'], $this->logCallback(), $ctx['subNamespace']);
        }

        $this->output("✅ Created Service {$ctx['displayName']} successfully.", 'info');
    }

    protected function generateService(array $ctx, bool $force): void
    {
        $replacements = [
            '{{name}}'                       => $ctx['model'],
            '{{serviceContractsNamespace}}'  => $ctx['serviceContractsNs'],
            '{{serviceImplNamespace}}'       => $ctx['serviceImplNs'],
        ];

        $interfacePath = app_path($ctx['serviceInterfacePath']);
        $implPath      = app_path($ctx['serviceImplPath']);

        $this->output("👉 Generating Service for {$ctx['displayName']}", 'info');
        $this->generateFileFromStub('simple-service/service-interface.stub', $interfacePath, $replacements, $force, $this->logCallback());
        $this->generateFileFromStub('simple-service/service.stub',           $implPath,      $replacements, $force, $this->logCallback());
    }
}
