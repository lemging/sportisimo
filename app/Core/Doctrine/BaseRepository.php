<?php

namespace App\Core\Doctrine;

use App\Core\Doctrine\QueryObject\Exceptions\QueryException;
use App\Core\Doctrine\QueryObject\Persistence\Query;
use App\Core\Doctrine\QueryObject\QueryObject;
use App\Core\Doctrine\QueryObject\QueryObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;


abstract class BaseRepository extends QueryObjectRepository
{
	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param array $criteria parameter can be skipped
	 * @param string $value mandatory
	 * @param array|string $orderBy parameter can be skipped
	 * @param string $key optional
	 *
	 * @throws QueryException
	 * @return array
	 */
	public function findPairs($criteria, $value, $orderBy, $key = null): array
	{
		if (!is_array($criteria)) {
			$key = $orderBy;
			$orderBy = $value;
			$value = $criteria;
			$criteria = [];
		}

//		if (!is_array($orderBy)) {
//			$key = $orderBy;
//		}

		if ($key === null) {
			$key = $this->getClassMetadata()->getSingleIdentifierFieldName();
		}

		/** @var QueryBuilder $qb */
		$qb = $this->createQueryBuilder('e')
			->select(["e.$value", "e.$key"])
			->resetDQLPart('from')->from($this->getEntityName(), 'e', 'e.' . $key);

		if (is_array($criteria) && !empty($criteria)) {
			$qb->where($criteria);
		}

		$qb->orderBy('e.' . $orderBy);

		$query = $qb->getQuery();

//		try {
			return array_map(static function ($row) {
				return reset($row);
			}, $query->getResult(AbstractQuery::HYDRATE_ARRAY));
//		} catch (\Exception $e) {
//			throw $this->handleException($e, $query);
//		}
	}

	public function fetchOne(Query $queryObject)
	{
		try {
			return $queryObject->fetchOne($this);
		} catch (NoResultException $e) {
			return null;
		} catch (NonUniqueResultException $e) { // this should never happen!
			throw new \Exception("You have to setup your query calling ->setMaxResult(1).", 0, $e);
		} catch (\Exception $e) {
			throw $this->handleQueryException($e, $queryObject);
		}
	}

	private function handleQueryException(\Exception $e, Query $queryObject)
	{
		$lastQuery = $queryObject instanceof QueryObject ? $queryObject->getLastQuery() : null;

		return new QueryException($e, $lastQuery, '[' . get_class($queryObject) . '] ' . $e->getMessage());
	}

	private function handleException(\Exception $e, Query $query = null, $message = null)
	{
		if ($e instanceof QueryException) {
			return new QueryException($e, $query, $message);
		}

		return $e;
	}
}
