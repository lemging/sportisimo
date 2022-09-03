<?php

namespace App\Core\Doctrine\QueryObject\Helpers;

use Doctrine\ORM\QueryBuilder;

interface IQueryFilter
{
	public function apply(QueryBuilder $queryBuilder): void;
}
