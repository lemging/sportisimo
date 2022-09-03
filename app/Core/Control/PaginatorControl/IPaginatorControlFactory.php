<?php declare(strict_types = 1);

namespace App\Core\Control\PaginatorControl;

use Nette\Utils\Paginator;

interface IPaginatorControlFactory
{
    public function create(Paginator $paginator): PaginatorControl;
}
