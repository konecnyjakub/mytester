<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

use MyTester\Bridges\NetteDI\ContainerFactory;
use Nette\Application\Request;
use Nette\Application\UI\Component;
use Nette\InvalidArgumentException;
use ReflectionClass;

/**
 * @author Jakub Konečný
 */
trait TComponent
{
    private ?PresenterMock $presenterMock = null;

    protected function attachToPresenter(Component $component, ?string $name = null): void
    {
        if ($name === null) {
            $name = $component->getName();
            if ($name === null) {
                $name = (new ReflectionClass($component))->getShortName();
            }
        }

        if ($this->presenterMock === null) {
            /** @var PresenterMock $presenterMock */
            $presenterMock = ContainerFactory::create()->getService("mytester.presenterMock");
            $this->presenterMock = $presenterMock;
        }

        $this->presenterMock->onStartup[] = function (PresenterMock $presenter) use ($component, $name): void {
            try {
                $presenter->removeComponent($presenter->getComponent($name));
            } catch (InvalidArgumentException) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
            }
            $presenter->addComponent($component, $name);
        };
        $this->presenterMock->run(new Request('Foo'));
    }

    /**
     * @param mixed[] $params
     */
    private function getRenderOutput(Component $control, array $params = [], string $renderMethod = "render"): string
    {
        if ($control->getParent() === null) {
            $this->attachToPresenter($control);
        }
        ob_start();
        /** @var callable $callback */
        $callback = [$control, $renderMethod];
        call_user_func_array($callback, $params);
        return (string) ob_get_clean();
    }

    /**
     * @param mixed[] $params
     */
    protected function assertRenderOutput(
        Component $control,
        string $expected,
        array $params = [],
        string $renderMethod = "render"
    ): void {
        $this->assertSame($expected, $this->getRenderOutput($control, $params, $renderMethod));
    }

    /**
     * @param mixed[] $params
     */
    protected function assertRenderOutputFile(
        Component $control,
        string $filename,
        array $params = [],
        string $renderMethod = "render"
    ): void {
        $this->assertMatchesFile($filename, $this->getRenderOutput($control, $params, $renderMethod));
    }
}
