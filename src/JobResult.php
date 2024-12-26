<?php
declare(strict_types=1);

namespace MyTester;

enum JobResult
{
    case PASSED;
    case SKIPPED;
    case FAILED;
    case WARNING;

    public function output(): string
    {
        return match ($this) {
            self::PASSED => ".",
            self::SKIPPED => "s",
            self::FAILED => "F",
            self::WARNING => "W",
        };
    }

    public static function fromJob(Job $job): self
    {
        if ($job->skip !== false) {
            return self::SKIPPED;
        } elseif (str_contains($job->output, " failed. ") || str_starts_with($job->output, "Error: ")) {
            return self::FAILED;
        } elseif (str_starts_with($job->output, "Warning: ")) {
            return self::WARNING;
        }
        return self::PASSED;
    }
}
