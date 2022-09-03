<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use Doctrine\ORM\QueryBuilder;

trait OrderByQueryObjectTrait
{
	/** @var string */
	private $orderCriteria = [];

	public function addOrderBy(string $sort, string $order): void
	{
		$this->orderCriteria[] = [$sort, $order];
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @internal
	 */
	public function doOrderBy(QueryBuilder $queryBuilder): void
	{
		foreach ($this->orderCriteria as [$sort, $order]) {
			$queryBuilder->orderBy($sort, $order);
		}
	}
}
