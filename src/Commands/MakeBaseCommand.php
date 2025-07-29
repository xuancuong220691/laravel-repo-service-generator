<?php

namespace CuongNX\RepoServiceGenerator\Commands;

use CuongNX\RepoServiceGenerator\Traits\ConsoleOutputTrait;
use Illuminate\Console\Command;
use CuongNX\RepoServiceGenerator\Traits\StubTrait;

class MakeBaseCommand extends Command
{
    use StubTrait, ConsoleOutputTrait;

    protected $signature = 'cuongnx:make-base {--f : Overwrite existing files} {--force : Overwrite existing files}';
    protected $description = 'Generate base Repository & Service with interfaces, and auto-bind them.';

    protected array $baseFiles = [
        'Repositories/Contracts/BaseRepositoryInterface.php' => 'base/base-repository-interface.stub',
        'Repositories/Eloquent/BaseRepository.php'           => 'base/base-repository.stub',
        'Services/Contracts/BaseServiceInterface.php'        => 'base/base-service-interface.stub',
        'Services/BaseService.php'                           => 'base/base-service.stub',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->initFilesystem();
    }

    public function handle(): int
    {
        $basePath = app_path();
        $force    = ($this->option('force') || $this->option('f'));

        $this->output("👉 Creating base files in: {$basePath}", 'info');
        foreach ($this->baseFiles as $target => $stubPath) {
            $targetPath = $basePath . '/' . $target;

            if ($this->files->exists($targetPath) && !$force) {
                $this->output("⚠️ Skipped: {$target} already exists. Use --force to overwrite.", 'warn');
                continue;
            }

            $content = $this->getStub($stubPath, $this->logCallback());
            $this->makeDirectory($targetPath);
            $this->files->put($targetPath, $content);
            $this->output("☑️  Created: {$target}", 'info');
        }

        $this->output("✅ Base files created successfully.", 'info');
        return self::SUCCESS;
    }
}
