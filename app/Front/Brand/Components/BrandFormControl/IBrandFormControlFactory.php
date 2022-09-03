<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandFormControl;

use App\Front\Brand\Objects\BrandData;
use App\Model\Brand\Entities\BrandCategory;

interface IBrandFormControlFactory
{
    public function create(?BrandData $brandData, BrandCategory $brandCategory): BrandFormControl;
}
