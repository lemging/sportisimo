<?php

namespace App\Model\Brand\Queries;

use App\Core\Doctrine\QueryObject\Persistence\Queryable;
use App\Core\Doctrine\QueryObject\QueryObject;
use App\Front\Brand\Enums\BrandsOrderEnum;
use App\Model\Brand\Entities\BrandCategory;
use Doctrine\ORM\QueryBuilder;

class BrandQuery extends QueryObject
{
	/** @var callable[] */
	protected $filters = [];

	public function withCategory(BrandCategory $category): self
    {
		$this->filters[] = static function (QueryBuilder $queryBuilder) use ($category) {
			$queryBuilder->andWhere('b.category = :category')
				->setParameter('category', $category);
		};

		return $this;
	}

	protected function doCreateQuery(Queryable $dao): QueryBuilder
    {
        $queryBuilder = $dao->createQueryBuilder('b', 'b.id')
			->select('b');


		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}

		$queryBuilder->addOrderBy('b.id');

		return $queryBuilder;
	}

	public function postFetch(Queryable $repository, \Iterator $iterator)
	{
	}

    public function sortByOrder(int $order): self
    {
        $this->filters[] = static function (QueryBuilder $queryBuilder) use ($order) {
            switch ($order) {
                case BrandsOrderEnum::SORT_BY_ID:
                    $queryBuilder->addOrderBy('b.id');
                    break;
                case BrandsOrderEnum::SORT_BY_TITLE:
                    $queryBuilder->addOrderBy('b.title');
                    break;
                case BrandsOrderEnum::SORT_BY_TITLE_UP:
                    $queryBuilder->addOrderBy('b.title', 'desc');
                    break;
            }
        };

        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->filters[] = static function (QueryBuilder $queryBuilder) use ($title) {
            $queryBuilder->andWhere('b.title = :title')
                ->setParameter('title', $title);
        };

        return $this;
    }
}
