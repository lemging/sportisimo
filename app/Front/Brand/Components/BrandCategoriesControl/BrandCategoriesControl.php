<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandCategoriesControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use App\Front\Brand\Components\BrandFormControl\BrandFormControl;
use App\Front\Brand\Components\BrandFormControl\IBrandFormControlFactory;
use App\Front\Brand\Objects\BrandData;
use App\Model\Brand\BrandFacade;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Template;

class BrandCategoriesControl extends AbstractControl
{
    use TRenderable;

    public function __construct(
        private BrandFacade $brandFacade,
        private array $brandCategories,
        private IBrandFormControlFactory $brandFormControlFactory,
    )
    {
    }

    public function setDefaultVariables(Template $template): void
    {
        parent::setDefaultVariables($template);

        $template->brandCategories = $this->brandCategories;
    }

    protected function createComponentBrandForm(): Multiplier
    {
        return new Multiplier(function($brandCategoryId): BrandFormControl {
            $category = $brandCategoryId !== null ? $this->brandFacade->findBrandCategory(intval($brandCategoryId)) : null;

            $control = $this->brandFormControlFactory->create(
                null,
                $category,
            );

            $control->onValidate[] = function(Form $form, BrandData $brandData): void {
                if ($this->brandFacade->doesBrandWithTitleExist($brandData->title)) {
                    $this->flashMessage('Nepodařilo se přidat. Značka s tímto názvem už existuje', 'failure');
                    $this->getPresenter()->redirect('this');
                }
            };

            $control->onSave[] = function(Form $form, BrandData $brandData): void {
                $this->brandFacade->addNewBrand($brandData);

                $this->flashMessage('Značka úspěšně přidána', 'success');
                $this->getPresenter()->redirect('this');
            };

            return $control;
        });
    }
}
