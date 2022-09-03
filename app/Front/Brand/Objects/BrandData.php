<?php declare(strict_types = 1);

namespace App\Front\Brand\Objects;

use App\Model\Brand\Entities\Brand;
use App\Model\Brand\Entities\BrandCategory;

final class BrandData
{
    public string $title;

    public string $description;

    public BrandCategory $brandCategory;

    public static function createFromBrand(Brand $brand): self
    {
        $data = new self();
        $data->title = $brand->getTitle();
        $data->description = $brand->getDescription();
        $data->brandCategory = $brand->getCategory();

        return $data;
    }
}
