<?php declare(strict_types = 1);

namespace App\Front\Brand\Component\BrandsControl;

use App\Model\Brand\Entities\Brand;

interface IBrandsControlFactory
{
    /**
     * @param array<Brand> $brands
     */
    public function create(array $brands): BrandsControl;
}
