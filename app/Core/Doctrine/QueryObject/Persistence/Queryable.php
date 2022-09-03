<?php

namespace App\Core\Doctrine\QueryObject\Persistence;

use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

interface Queryable
{
	/**
	 * @param string $alias
	 * @param ?string $indexBy
	 * @return QueryBuilder
	 */
	public function createQueryBuilder($alias = null, $indexBy = null);

	public function createQuery(?string $dql = null): \Doctrine\ORM\Query;

	public function createNativeQuery(string $sql, ResultSetMapping $rsm): NativeQuery;
}
