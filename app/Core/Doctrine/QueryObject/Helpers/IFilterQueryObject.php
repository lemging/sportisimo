<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use Doctrine\ORM\QueryBuilder;

interface IFilterQueryObject
{
	/**
	 * @param QueryBuilder $queryBuilder
	 * @internal
	 */
	public function doFilter(QueryBuilder $queryBuilder): void;
}
