<?php declare(strict_types = 1);

namespace App\Model\Brand;

use App\Front\Brand\Objects\BrandData;
use App\Model\Brand\Entities\Brand;
use App\Model\Brand\Entities\BrandCategory;
use App\Model\Brand\Managers\BrandCategoryManager;
use App\Model\Brand\Managers\BrandManager;
use App\Model\Brand\Queries\BrandQuery;
use App\Model\Brand\Repositories\BrandCategoryRepository;
use App\Model\Brand\Repositories\BrandRepository;
use Nette\Utils\Paginator;

class BrandFacade
{
    public function __construct(
        private BrandRepository $brandRepository,
        private BrandCategoryRepository $brandCategoryRepository,
        private BrandManager $brandManager,
        private BrandCategoryManager $brandCategoryManager,
    ) {
    }

    public function addNewBrand(BrandData $brandData): Brand
    {
        $brand = new Brand($brandData->title, $brandData->description, $brandData->brandCategory);
        $this->brandManager->saveBrand($brand);

        return $brand;
    }

    public function updateExistingBrand(int $id, BrandData $brandData): void
    {
        /** @var Brand $brand */
        $brand = $this->brandRepository->find($id);
        $brand->setTitle($brandData->title);
        $brand->setDescription($brandData->description);
        $brand->setCategory($brandData->brandCategory);

        $this->brandManager->saveBrand($brand);
    }

    public function createBrandCategory(string $title, string $description): void
    {
        $this->brandCategoryManager->createBrandCategory($title, $description);
    }

    public function findBrandCategory(int $id): ?BrandCategory
    {
        return $this->brandCategoryRepository->find($id);
    }

    public function findBrand(int $id): ?Brand
    {
        return $this->brandRepository->find($id);
    }

    /**
     * @return array<BrandCategory>
     */
    public function findBrandCategories(): array
    {
        return $this->brandCategoryRepository->findAll();
    }

    /**
     * @return array<Brand>
     */
    public function findBrands(): array
    {
        return $this->brandRepository->findAll();
    }

    public function fetch(BrandQuery $brandQuery)
    {
        return $this->brandRepository->fetch($brandQuery);
    }

    /**
     * @return array{brands: array<int, Brand>, paginator: Paginator}
     */
    public function findBrandsForList(int $page, int $limit, int $order): array
    {
        $query = $this->createBrandQuery();

        $query->sortByOrder($order);

        $brands = $this->fetch($query);

        $paginator = new Paginator();
        $paginator->setItemsPerPage($limit);
        $paginator->setPage($page);
        $paginator->setItemCount($brands->count());

        return [
            'brands' => $brands->applyPaginator($paginator)->toArray(),
            'paginator' => $paginator,
        ];
    }

    public function createBrandQuery(?BrandCategory $brandCategory = null): BrandQuery
    {
        $query = (new BrandQuery());

        if ($brandCategory) {
            $query->withCategory($brandCategory);
        }

        return $query;
    }

    public function doesBrandWithTitleExist(string $title): bool
    {
        $query = $this->createBrandQuery()->withTitle($title);

        return !$this->fetch($query)->isEmpty();
    }

    public function removeBrand(Brand $brand)
    {
        $this->brandManager->deleteBrand($brand);
    }
}
