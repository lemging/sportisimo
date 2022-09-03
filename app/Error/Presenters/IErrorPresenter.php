<?php declare(strict_types = 1);

namespace App\Error\Presenter;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;

interface IErrorPresenter extends IPresenter
{
    public function run(Request $request): Response;
}
