<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\ISkipChecker;

/**
 * Skip checker for .phpt files
 *
 * @author Jakub Konečný
 */
final readonly class SkipChecker implements ISkipChecker
{
    public function __construct(private AnnotationsEngine $annotationsEngine, private Runner $runner)
    {
    }

    /**
     * @param string $class filename
     */
    public function shouldSkip(string $class, string $method = ""): bool
    {
        /** @var string|null $code */
        $code = $this->annotationsEngine->getAnnotation(AnnotationsEngine::ANNOTATION_SKIP, $class);
        if ($code === null) {
            return false;
        }
        $result = $this->runner->runCode($code);
        return str_starts_with($result, "skip");
    }
}
