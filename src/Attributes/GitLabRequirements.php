<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * GitLabRequirements attribute
 * Specifies GitLab Requirements that this test checks
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class GitLabRequirements
{
    /**
     * @var int[]
     */
    private array $ids;

    public function __construct(int ...$requirementsIds)
    {
        $this->ids = $requirementsIds;
    }

    /**
     * @return int[]
     */
    public function getValue(): array
    {
        return $this->ids;
    }
}
