<?php

namespace CuongNX\RepoServiceGenerator\Traits;

use Illuminate\Filesystem\Filesystem;

trait StubTrait
{
    protected Filesystem $files;

    protected function initFilesystem(): void
    {
        if (!isset($this->files)) {
            $this->files = new Filesystem();
        }
    }

    protected function getStub(string $relativePath, ?callable $outputHandler = null): string
    {
        $stub = __DIR__ . '/../../stubs/' . $relativePath;

        if (!$this->files->exists($stub)) {
            $outputHandler("❌ Stub not found: {$stub}", 'error');
            exit(1);
        }

        return $this->files->get($stub);
    }

    protected function makeDirectory(string $path): void
    {
        $dir = dirname($path);

        if (!$this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }
    }

    protected function generateFileFromStub(
        string $stubName,
        string $targetPath,
        array $replacements,
        bool $force,
        ?callable $outputHandler = null
    ): void {
        if (!$force && $this->files->exists($targetPath)) {
            if ($outputHandler) {
                $outputHandler("❌ File {$targetPath} already exists. Use --f to overwrite.", 'warn');
            }
            return;
        }

        $content = $this->getStub($stubName, $outputHandler);

        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $this->makeDirectory($targetPath);
        $this->files->put($targetPath, $content);

        if ($outputHandler) {
            $outputHandler("☑️  Created: {$targetPath}");
        }
    }

}
