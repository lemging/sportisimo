<?php declare(strict_types = 1);

namespace App\Common\Core\Control;

use App\Common\Core\BaseTrait\TTemplateLocator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Utils\Strings;
use stdClass;

abstract class AbstractControl extends Control
{
    use TTemplateLocator;

    public function createTemplate(): Template
    {
        $template = parent::createTemplate();

        $file = $this->findTemplateFile();

        if ($file !== null) {
            $template->setFile($file);
        }

        $this->setDefaultVariables($template);

        return $template;
    }

    protected function setDefaultVariables(Template $template): void
    {
    }

    public function flashMessage($message, string $type = 'info'): stdClass
    {
        $presenter = $this->getPresenter();
        $flash = $presenter->flashMessage($message, $type);
        $presenter->redrawControl('flashes');

        return $flash;
    }

    protected function getControlName(): string
    {
        $reflection = new \ReflectionClass($this);

        return Strings::webalize(($reflection->getShortName()));
    }
}
