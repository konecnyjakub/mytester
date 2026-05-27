<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use Nette\Application\IPresenter;
use Nette\InvalidStateException;

/**
 * Test suite for trait TComponent
 *
 * @author Jakub Konečný
 */
#[TestSuite("TComponent")]
#[Group("nette")]
#[RequiresEnvVariable("MYTESTER_NETTE_DI")]
final class TComponentTest extends TestCase
{
    use TComponent;

    public function testAttachToPresenter(): void
    {
        $control = new Component();
        $this->assertThrowsException(
            function () use ($control) {
                $control->lookup(IPresenter::class); // @phpstan-ignore argument.type, argument.templateType
            },
            InvalidStateException::class,
            "Component type of '" . Component::class . "' is not attached to '" . IPresenter::class . "'."
        );
        $this->attachToPresenter($control);
        // @phpstan-ignore argument.type, argument.templateType
        $this->assertType(IPresenter::class, $control->lookup(IPresenter::class));
    }

    public function testAssertRenderOutput(): void
    {
        $control = new Component();
        $this->assertRenderOutput($control, "<div>abc</div>");
        $this->assertRenderOutput($control, "<div>abc12</div>", ["one" => 1, "two" => 2, ]);
    }

    public function testAssertRenderOutputFile(): void
    {
        $control = new Component();
        $this->assertRenderOutputFile($control, __DIR__ . "/component.txt");
        $this->assertRenderOutputFile($control, __DIR__ . "/component_params.txt", ["one" => 1, "two" => 2, ]);
    }
}
