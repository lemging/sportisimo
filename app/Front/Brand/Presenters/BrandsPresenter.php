<?php

declare(strict_types=1);

namespace App\Front\Brand\Presenters;

use App\Core\Control\OrderControl\IOrderControlFactory;
use App\Core\Control\OrderControl\OrderControl;
use App\Core\Control\PaginatorControl\IPaginatorControlFactory;
use App\Core\Control\PaginatorControl\PaginatorControl;
use App\Front\Brand\Component\BrandsControl\BrandsControl;
use App\Front\Brand\Component\BrandsControl\IBrandsControlFactory;
use App\Front\Brand\Components\BrandCategoriesControl\BrandCategoriesControl;
use App\Front\Brand\Components\BrandCategoriesControl\IBrandCategoriesControlFactory;
use App\Front\Brand\Components\BrandFormControl\IBrandFormControlFactory;
use App\Front\Brand\Enums\BrandsOrderEnum;
use App\Model\Brand\BrandFacade;
use App\Model\Brand\Entities\Brand;
use Exception;
use Nette;
use Nette\Utils\Paginator;

class BrandsPresenter extends Nette\Application\UI\Presenter
{
    private Paginator $paginator;

    /** @persistent */
    public int $limit = 10;

    /** @persistent */
    public int $page = 0;

    /**
     * @var array<Brand>
     */
    private array $brands;

    private int $order;

    public function __construct(
        private BrandFacade $brandFacade,
        private IBrandsControlFactory $brandsControlFactory,
        private IPaginatorControlFactory $paginatorControlFactory,
        private IOrderControlFactory $orderControlFactory,
        private IBrandFormControlFactory $brandFormControlFactory,
        private IBrandCategoriesControlFactory $brandCategoriesControlFactory,
    )
    {
    }

    public function actionDefault(int $order = BrandsOrderEnum::SORT_BY_ID): void
    {
        $this->order = $order;

        $result = $this->brandFacade->findBrandsForList($this->page, $this->limit, $this->order);
        $this->brands = $result['brands'];
        $this->paginator = $result['paginator'];
    }

    protected function createComponentBrands(): BrandsControl
    {
        $control = $this->brandsControlFactory->create($this->brands);
        $control->onDelete[] = function(Brand $brand): void {
            try {
                $this->brandFacade->removeBrand($brand);
                $this->flashMessage('Značka úspěšně odstraněna', 'success');
            } catch (Exception $e) {
                $this->flashMessage('Nepodařilo se odstranit', 'failure');
            }

            $this->redirect('this');
        };

        return $control;
    }

    protected function createComponentPaginator(): PaginatorControl
    {
        $control = $this->paginatorControlFactory->create($this->paginator);

        $control->onPageChange[] = function(int $page): void {
            $this->page = $page;
            $this->redirect('this');
        };

        $control->onLimitChange[] = function(int $limit): void {
            $this->limit = $limit;
            $this->redirect('this');
        };

        return $control;
    }

    protected function createComponentOrder(): OrderControl
    {
        $control = $this->orderControlFactory->create($this->order, BrandsOrderEnum::getOrders());

        $control->onChangeOrder[] = function(int $order) {
            $this->redirect('this', [$order]);
        };

        return $control;
    }

    protected function createComponentBrandCategories(): BrandCategoriesControl
    {
        return $this->brandCategoriesControlFactory->create($this->brandFacade->findBrandCategories());
    }
}
