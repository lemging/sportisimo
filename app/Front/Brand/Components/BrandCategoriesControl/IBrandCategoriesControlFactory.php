<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandCategoriesControl;

use App\Model\Brand\Entities\Brand;

interface IBrandCategoriesControlFactory
{
    /**
     * @param array<Brand> $brandCategories
     */
    public function create(array $brandCategories): BrandCategoriesControl;
}
