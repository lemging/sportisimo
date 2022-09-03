<?php

namespace App\Core\Doctrine\QueryObject;

use Doctrine;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Core\Doctrine\QueryObject\Exceptions\QueryException;
use App\Core\Doctrine\QueryObject\Persistence\Query;
use App\Core\Doctrine\QueryObject\Persistence\Queryable;

class QueryObjectRepository extends Doctrine\ORM\EntityRepository implements Queryable
{
	/**
	 * @param QueryObject $queryObject
	 * @param int $hydrationMode
	 * @return ResultSet|array
	 */
	public function fetch(QueryObject $queryObject, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
	{
		try {
			return $queryObject->fetch($this, $hydrationMode);
		} catch (\Exception $e) {
			throw $this->handleQueryException($e, $queryObject);
		}
	}

	private function handleQueryException(\Exception $e, Query $queryObject): QueryException
	{
		$lastQuery = $queryObject instanceof QueryObject ? $queryObject->getLastQuery() : null;

		return new QueryException($e, $lastQuery, '[' . get_class($queryObject) . '] ' . $e->getMessage());
	}

	public function createQueryBuilder($alias = null, $indexBy = null)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		if ($alias !== null) {
			$qb->select($alias)->from($this->getEntityName(), $alias, $indexBy);
		}
		return $qb;
	}

	public function createQuery(?string $dql = null): \Doctrine\ORM\Query
	{
		$dql = implode(' ', func_get_args());
		return $this->getEntityManager()->createQuery($dql);
	}

	public function createNativeQuery(string $sql, ResultSetMapping $rsm): NativeQuery
	{
		return $this->getEntityManager()->createNativeQuery($sql, $rsm);
	}
}
