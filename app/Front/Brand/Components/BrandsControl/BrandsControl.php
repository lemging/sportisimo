<?php declare(strict_types = 1);

namespace App\Front\Brand\Component\BrandsControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use App\Front\Brand\Components\BrandControl\BrandControl;
use App\Front\Brand\Components\BrandControl\IBrandControlFactory;
use App\Model\Brand\Entities\Brand;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Template;

class BrandsControl extends AbstractControl
{
    use TRenderable;

    /** @var array<callable> */
    public array $onDelete = [];

    public function __construct(
        private IBrandControlFactory $brandControlFactory,
        private array $brands,
    )
    {
    }

    public function setDefaultVariables(Template $template): void
    {
        parent::setDefaultVariables($template);
        $template->brands = $this->brands;
    }

    protected function createComponentBrand(): Multiplier
    {
        return new Multiplier(function($key): BrandControl {
            $control = $this->brandControlFactory->create($this->brands[$key]);
            $control->onDelete[] = function (Brand $brand): Void {
                $this->onDelete($brand);
            };

            return $control;
        });
    }
}
