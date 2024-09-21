<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

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
final class TComponentTest extends TestCase
{
    use TComponent;

    public function testAttachToPresenter(): void
    {
        if (!isset($_ENV["MYTESTER_NETTE_DI"])) {
            $this->markTestSkipped("This test needs Nette DI container.");
        }
        $control = new Component();
        $this->assertThrowsException(
            function () use ($control) {
                $control->lookup(IPresenter::class);
            },
            InvalidStateException::class,
            "Component of type '" . Component::class . "' is not attached to '" . IPresenter::class . "'."
        );
        $this->attachToPresenter($control);
        $this->assertType(IPresenter::class, $control->lookup(IPresenter::class));
    }

    public function testAssertRenderOutput(): void
    {
        if (!isset($_ENV["MYTESTER_NETTE_DI"])) {
            $this->markTestSkipped("This test needs Nette DI container.");
        }
        $control = new Component();
        $this->assertRenderOutput($control, "<div>abc</div>");
        $this->assertRenderOutput($control, "<div>abc12</div>", ["one" => 1, "two" => 2, ]);
    }

    public function testAssertRenderOutputFile(): void
    {
        if (!isset($_ENV["MYTESTER_NETTE_DI"])) {
            $this->markTestSkipped("This test needs Nette DI container.");
        }
        $control = new Component();
        $this->assertRenderOutputFile($control, __DIR__ . "/component.txt");
        $this->assertRenderOutputFile($control, __DIR__ . "/component_params.txt", ["one" => 1, "two" => 2, ]);
    }
}
