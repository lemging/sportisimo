<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use App\Front\Brand\Components\BrandFormControl\BrandFormControl;
use App\Front\Brand\Components\BrandFormControl\IBrandFormControlFactory;
use App\Front\Brand\Objects\BrandData;
use App\Model\Brand\BrandFacade;
use App\Model\Brand\Entities\Brand;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Template;
use Nette\Forms\Controls\BaseControl;

class BrandControl extends AbstractControl
{
    use TRenderable;

    /** @var array<callable> */
    public array $onDelete = [];

    public function __construct(
        private BrandFacade $brandFacade,
        private IBrandFormControlFactory $brandFormControlFactory,
        private Brand $brand,
    ) {
    }

    public function setDefaultVariables(Template $template): void
    {
        parent::setDefaultVariables($template);
        $template->brand = $this->brand;
    }

    protected function createComponentBrandForm(): Multiplier
    {
        return new Multiplier(function($brandId): BrandFormControl {
            $brand = $this->brandFacade->findBrand(intval($brandId));

            $control = $this->brandFormControlFactory->create(
                BrandData::createFromBrand($brand),
                $brand->getCategory(),
            );

            $control->onValidate[] = function(Form $form, BrandData $brandData) use ($brand): void {
                if ($brandData->title !== $brand->getTitle() &&
                    $this->brandFacade->doesBrandWithTitleExist($brandData->title)) {

                    $this->flashMessage('Nepodařilo se upravit. Značka s tímto názvem už existuje', 'failure');
                    $this->getPresenter()->redirect('this');
                }
            };

            $control->onSave[] = function(Form $form, BrandData $brandData) use ($brand): void {
                $this->brandFacade->updateExistingBrand($brand->getId(), $brandData);

                $this->flashMessage('Značka úspěšně upravena','failure');
                $this->getPresenter()->redirect('this', ['brand' => $brand]);
            };

            return $control;
        });
    }

    public function handleRemoveBrand(): void
    {
        $this->onDelete($this->brand);
    }
}
