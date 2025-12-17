<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use MyTester\SkipAttribute;

/**
 * Requires package attribute
 * Defines a Composer package required for a test (suite)
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPackage implements SkipAttribute
{
    public function __construct(public string $packageName, public ?string $version = null)
    {
        if ($this->version !== null && !InstalledVersions::isInstalled("composer/semver")) {
            trigger_error(
                "Specifying a version constraint for package requires package composer/semver",
                E_USER_ERROR
            );
        }
    }

    public function getSkipValue(): ?string
    {
        if (!InstalledVersions::isInstalled($this->packageName)) {
            return "package $this->packageName is not installed";
        }
        if (
            $this->version !== null &&
            !InstalledVersions::satisfies(new VersionParser(), $this->packageName, $this->version)
        ) {
            return "package $this->packageName is not installed in version $this->version";
        }
        return null;
    }

    public function getValue(): string
    {
        return $this->packageName . (is_string($this->version) ? " $this->version" : "");
    }
}
