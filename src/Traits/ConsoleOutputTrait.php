<?php

namespace CuongNX\RepoServiceGenerator\Traits;

trait ConsoleOutputTrait
{
    protected function output(string $message, string $type = 'line'): void
    {
        match ($type) {
            'info'  => $this->info($message),
            'warn'  => $this->warn($message),
            'error' => $this->error($message),
            default => $this->line($message),
        };
    }

    protected function logCallback(): \Closure
    {
        return function ($msg, $type = 'line') {
            $this->output($msg, $type);
        };
    }
}
