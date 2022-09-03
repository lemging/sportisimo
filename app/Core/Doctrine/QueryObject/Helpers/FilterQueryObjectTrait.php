<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use Doctrine\ORM\QueryBuilder;

trait FilterQueryObjectTrait
{
	/** @var callable[] */
	protected $filter = [];

	public function addFilter(callable $filter): void
	{
		$this->filter[] = $filter;
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @internal
	 */
	public function doFilter(QueryBuilder $queryBuilder): void
	{
		foreach ($this->filter as $filter) {
			$filter($queryBuilder);
		}
	}
}
