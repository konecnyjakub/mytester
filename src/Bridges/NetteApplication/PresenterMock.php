<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteApplication;

use Nette\Application\UI\Presenter;

/**
 * @author Jakub Konečný
 * @internal
 *
 * @method void onStartup(self $presenter)
 * @property callable[] $onStartup
 */
final class PresenterMock extends Presenter
{
    public function __construct()
    {
        parent::__construct();
        $this->autoCanonicalize = false;
    }

    protected function startup(): void
    {
        parent::startup();
        $this->onStartup($this);
    }

    protected function afterRender(): void
    {
        parent::afterRender();
        $this->sendPayload();
    }

    public function isAjax(): bool
    {
        return false;
    }
}
