<?php

namespace App\Core\Doctrine\QueryObject;

use App\Core\Doctrine\QueryObject\Exceptions\InvalidStateException;
use App\Core\Doctrine\QueryObject\Exceptions\QueryException;
use App\Core\Doctrine\QueryObject\Persistence\Queryable;
use Doctrine\ORM;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as ResultPaginator;

use Nette\Utils\Paginator as UIPaginator;

/**
 * ResultSet accepts a Query that it can then paginate and count the results for you
 *
 * <code>
 * public function renderDefault()
 * {
 *    $articles = $this->articlesDao->fetch(new ArticlesQuery());
 *    $articles->applyPaginator($this['vp']->paginator);
 *    $this->template->articles = $articles;
 * }
 *
 * protected function createComponentVp()
 * {
 *    return new VisualPaginator;
 * }
 * </code>.
 *
 * It automatically counts the query, passes the count of results to paginator
 * and then reads the offset from paginator and applies it to the query so you get the correct results.
 *
 * @author Filip Procházka <filip@prochazka.su>
 */
class ResultSet implements \Countable, \IteratorAggregate
{
	/** @var int */
	private $totalCount;

	/** @var Query */
	private $query;

	/** @var QueryObject|null */
	private $queryObject;

	/** @var Queryable|null */
	private $repository;

	/** @var bool */
	private $fetchJoinCollection = true;

	/** @var bool|null */
	private $useOutputWalkers;

	/** @var \Iterator|null|\Traversable */
	private $iterator;

	/** @var bool */
	private $frozen = false;

	/**
	 * @param Query $query
	 * @param QueryObject $queryObject
	 * @param Queryable $repository
	 */
	public function __construct(Query $query, QueryObject $queryObject = null, Queryable $repository = null)
	{
		$this->query = $query;
		$this->queryObject = $queryObject;
		$this->repository = $repository;

//		if ($this->query instanceof ORM\NativeQuery) {
//			$this->fetchJoinCollection = false;
//		}
	}

	/**
	 * @param bool $fetchJoinCollection
	 * @return ResultSet
	 */
	public function setFetchJoinCollection($fetchJoinCollection)
	{
		$this->updating();

		$this->fetchJoinCollection = (bool)$fetchJoinCollection;
		$this->iterator = null;

		return $this;
	}

	/**
	 * @param bool|null $useOutputWalkers
	 * @return ResultSet
	 */
	public function setUseOutputWalkers($useOutputWalkers)
	{
		$this->updating();

		$this->useOutputWalkers = $useOutputWalkers;
		$this->iterator = null;

		return $this;
	}

	/**
	 * @return bool|null
	 */
	public function getUseOutputWalkers()
	{
		return $this->useOutputWalkers;
	}

	/**
	 * @return boolean
	 */
	public function getFetchJoinCollection()
	{
		return $this->fetchJoinCollection;
	}

	/**
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return ResultSet
	 */
	public function applyPaging($offset, $limit)
	{
		if ($this->query->getFirstResult() != $offset || $this->query->getMaxResults() != $limit) {
			$this->query->setFirstResult($offset);
			$this->query->setMaxResults($limit);
			$this->iterator = null;
		}

		return $this;
	}

	/**
	 * @param \Nette\Utils\Paginator $paginator
	 * @param int $itemsPerPage
	 * @return ResultSet
	 */
	public function applyPaginator(UIPaginator $paginator, $itemsPerPage = null)
	{
		if ($itemsPerPage !== null) {
			$paginator->setItemsPerPage($itemsPerPage);
		}

		$paginator->setItemCount($this->getTotalCount());
		$this->applyPaging($paginator->getOffset(), $paginator->getLength());

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		$count = $this->getTotalCount();
		$offset = $this->query->getFirstResult();

		return $count <= $offset;
	}

	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		if ($this->totalCount === null) {
			try {
				$this->frozen = true;

				$paginatedQuery = $this->createPaginatedQuery($this->query);

				if ($this->queryObject !== null && $this->repository !== null) {
					$this->totalCount = $this->queryObject->count($this->repository, $this, $paginatedQuery);
				} else {
					$this->totalCount = $paginatedQuery->count();
				}
			} catch (ORMException $e) {
				throw new QueryException($e, $this->query, $e->getMessage());
			}
		}

		return $this->totalCount;
	}

	public function getIterator($hydrationMode = ORM\AbstractQuery::HYDRATE_OBJECT): \Iterator
	{
		if ($this->iterator !== null) {
			return $this->iterator;
		}

		$this->query->setHydrationMode($hydrationMode);

		try {
			$this->frozen = true;

			if ($this->fetchJoinCollection && ($this->query->getMaxResults() > 0 || $this->query->getFirstResult() > 0)) {
				$this->iterator = $this->createPaginatedQuery($this->query)->getIterator();
			} else {
				$this->iterator = new \ArrayIterator($this->query->getResult(null));
			}

			if ($this->queryObject !== null && $this->repository !== null) {
				$this->queryObject->postFetch($this->repository, $this->iterator);
			}

			return $this->iterator;
		} catch (ORMException $e) {
			throw new QueryException($e, $this->query, $e->getMessage());
		}
	}

	/**
	 * @param int $hydrationMode
	 * @return array
	 */
	public function toArray($hydrationMode = ORM\AbstractQuery::HYDRATE_OBJECT)
	{
		return iterator_to_array(clone $this->getIterator($hydrationMode), true);
	}

	/**
	 * @return mixed|null
	 */
	public function getFirstResultOrNull()
	{
		$this->query->setMaxResults(1);
		return $this->toArray()[0] ?? null;
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		if (method_exists($this->getIterator(), 'count')) {
			return $this->getIterator()->count();
		}

		throw new \Exception('unknown method count()');
	}

	/**
	 * @param Query $query
	 * @return ResultPaginator
	 */
	private function createPaginatedQuery(Query $query)
	{
		$paginated = new ResultPaginator($query, $this->fetchJoinCollection);
		$paginated->setUseOutputWalkers($this->useOutputWalkers);

		return $paginated;
	}

	private function updating()
	{
		if ($this->frozen !== false) {
			throw new InvalidStateException("Cannot modify result set, that was already fetched from storage.");
		}
	}
}
