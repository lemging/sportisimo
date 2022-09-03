<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use App\Core\Doctrine\QueryObject\QueryObject;
use Doctrine\ORM\QueryBuilder;
use App\Core\Doctrine\QueryObject\Persistence\Queryable;

abstract class BaseQueryObject extends QueryObject
{
	/** @var callable[] */
	protected $filter = [];

	/** @var callable[] */
	protected $select = [];

	/** @var callable[] */
	protected $postFetch = [];

	abstract protected function createBasicDql(Queryable $repository): QueryBuilder;

	protected function createDql(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $this->createBasicDql($repository);

		if ($this instanceof IFilterQueryObject) {
			$this->doFilter($queryBuilder);
		}
		return $queryBuilder;
	}

	protected function doCreateQuery(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $this->createDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($queryBuilder);
		}

		if ($this instanceof IOrderByQueryObject) {
			$this->doOrderBy($queryBuilder);
		}
		return $queryBuilder;
	}

	public function applyPaging(int $offset, int $limit): void
	{
		$this->select[] = static function (QueryBuilder $queryBuilder) use ($offset, $limit): void {
			$queryBuilder->setFirstResult($offset);
			$queryBuilder->setMaxResults($limit);
		};
	}

	public function postFetch(Queryable $repository, \Iterator $iterator): void
	{
		foreach ($this->postFetch as $modifier) {
			$modifier($repository, $iterator);
		}
	}
}
