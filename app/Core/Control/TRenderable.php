<?php declare(strict_types = 1);

namespace App\Common\Core\Control;

trait TRenderable
{
    public function render(): void
    {
        $this->getTemplate()->render();
    }
}
