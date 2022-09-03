<?php

namespace App\Model\Brand\Queries;

use App\Core\Doctrine\QueryObject\Persistence\Queryable;
use App\Core\Doctrine\QueryObject\QueryObject;
use Doctrine\ORM\QueryBuilder;

class BrandCategoryQuery extends QueryObject
{
	/** @var array<callable> */
	protected array $filters = [];

	protected function doCreateQuery(Queryable $dao): QueryBuilder
    {
		$queryBuilder = $dao->createQueryBuilder('bc', 'bc.id')
			->select('bc');

		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}

		$queryBuilder->addOrderBy('bc.id');

		return $queryBuilder;
	}

	public function postFetch(Queryable $repository, \Iterator $iterator)
	{
	}
}
