<?php declare(strict_types = 1);

namespace App\Front\Brand\Components\BrandFormControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use App\Front\Brand\Objects\BrandData;
use App\Model\Brand\Entities\BrandCategory;
use Nette\Application\UI\Form;

class BrandFormControl extends AbstractControl
{
    use TRenderable;

    /** @var array<callable> */
    public array $onSave = [];

    /** @var array<callable> */
    public array $onValidate = [];

    public function __construct(
        private ?BrandData $brandData,
        private BrandCategory $brandCategory,
    ) {
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setMappedType(BrandData::class);

        $form->addGroup('Značka');
        $form->addText('title', 'Název')
            ->setMaxLength(255)
            ->setRequired('Vyplňte prosím název');

        $form->addText('description', 'Popis')
            ->setMaxLength(255)
            ->setRequired('Vyplňte prosím popis');

        if ($this->brandData !== null) {
            $form->setDefaults($this->brandData);
        }

        $form->addSubmit('send', 'Uložit');

        $form->onValidate[] = function(Form $form, BrandData $brandData): void {
            $this->onValidate($form, $brandData);
        };

        $form->onSuccess[] = function(Form $form, BrandData $brandData): void {
            $brandData->brandCategory = $this->brandCategory;

            $this->onSave($form, $brandData);
        };

        return $form;
    }
}
