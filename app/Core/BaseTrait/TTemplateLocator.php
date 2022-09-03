<?php declare(strict_types = 1);

namespace App\Common\Core\BaseTrait;

use Nette\ComponentModel\Container;

trait TTemplateLocator
{
    protected function findTemplateFile(?string $part = null): ?string
    {
        $class = new \ReflectionClass($this);

        /** @var \ReflectionClass<Container> $parentClass */
        $parentClass = $class->getParentClass();

        if ($part === null) {
            $fileTemplate = $class->getShortName() . '.latte';
            $parentName = $parentClass->getShortName() . '.latte';
        } else {
            $fileTemplate = $class->getShortName() . $part . '.latte';
            $parentName = $parentClass->getShortName() . $part . '.latte';
        }

        $templateFile = \dirname((string) $class->getFileName()) . '/template/' . $fileTemplate;
        $parentTemplateFile = \dirname((string) $parentClass->getFileName()) . '/template/' . $parentName;

        return \file_exists($templateFile) ? $templateFile : $parentTemplateFile;
    }
}
