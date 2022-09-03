<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandControl;

use App\Model\Brand\Entities\Brand;

interface IBrandControlFactory
{
    public function create(Brand $brand): BrandControl;
}
