<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use Doctrine\ORM\QueryBuilder;

interface IOrderByQueryObject
{
	/**
	 * @param QueryBuilder $queryBuilder
	 * @internal
	 */
	public function doOrderBy(QueryBuilder $queryBuilder): void;
}
