<?php

declare(strict_types=1);

namespace App\Router;

use http\Exception;
use Nette;
use Nette\Application\BadRequestException;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {


        try {
            $router = new RouteList;
            $router->addRoute('brands', 'Front:Brands:default');
        } catch (BadRequestException) {
            throw new Nette\Neon\Exception();
        }

        return $router;
    }
}
