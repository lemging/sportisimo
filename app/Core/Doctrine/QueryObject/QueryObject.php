<?php

namespace App\Core\Doctrine\QueryObject;

use Doctrine;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Core\Doctrine\QueryObject\Exceptions\UnexpectedValueException;
use App\Core\Doctrine\QueryObject\Persistence\Query;
use App\Core\Doctrine\QueryObject\Persistence\Queryable;
use Nette;

/**
 * Purpose of this class is to be inherited and have implemented doCreateQuery() method,
 * which constructs DQL from your constraints and filters.
 *
 * QueryObject inheritors are great when you're printing a data to the user,
 * they may be used in service layer but that's not really suggested.
 *
 * Don't be afraid to use them in presenters
 *
 * <code>
 * $this->template->articles = $this->articlesRepository->fetch(new ArticlesQuery());
 * </code>
 *
 * or in more complex ways
 *
 * <code>
 * $productsQuery = new ProductsQuery();
 * $productsQuery
 *    ->setColor('green')
 *    ->setMaxDeliveryPrice(100)
 *    ->setMaxDeliveryMinutes(75);
 *
 * $productsQuery->size = 'big';
 *
 * $this->template->products = $this->productsDao->fetch($productsQuery);
 * </code>
 *
 * @method onPostFetch(QueryObject $self, Queryable $repository, \Iterator $iterator)
 *
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class QueryObject implements Query
{
	use Nette\SmartObject;

	/** @var array */
	public $onPostFetch = [];

	/** @var \Doctrine\ORM\Query */
	private $lastQuery;

	/** @var ResultSet */
	private $lastResult;

	/**
	 */
	public function __construct()
	{
	}

	/**
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	abstract protected function doCreateQuery(Queryable $repository);

	/**
	 * @param Queryable $repository
	 *
	 * @return \Doctrine\ORM\Query
	 */
	protected function getQuery(Queryable $repository)
	{
		$query = $this->toQuery($this->doCreateQuery($repository));

		if ($this->lastQuery && $this->lastQuery->getDQL() === $query->getDQL()) {
			$query = $this->lastQuery;
		}

		if ($this->lastQuery !== $query) {
			$this->lastResult = new ResultSet($query, $this, $repository);
		}

		return $this->lastQuery = $query;
	}

	/**
	 * @param Queryable $repository
	 */
	protected function doCreateCountQuery(Queryable $repository)
	{
	}

	/**
	 * @param Queryable $repository
	 * @param ResultSet $resultSet
	 * @param \Doctrine\ORM\Tools\Pagination\Paginator $paginatedQuery
	 * @return integer
	 */
	public function count(Queryable $repository, ResultSet $resultSet = null, Paginator $paginatedQuery = null): int
	{
		if ($query = $this->doCreateCountQuery($repository)) {
			return $this->toQuery($query)->getSingleScalarResult();
		}

//		if ($this->lastQuery && $this->lastQuery instanceof NativeQueryWrapper) {
//			$class = get_called_class();
//			throw new NotSupportedException("You must implement your own count query in $class::doCreateCountQuery(), Paginator from Doctrine doesn't support NativeQueries.");
//		}

		if ($paginatedQuery !== null) {
			return $paginatedQuery->count();
		}

		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(null);

		$paginatedQuery = new Paginator($query, $resultSet ? $resultSet->getFetchJoinCollection() : true);
		$paginatedQuery->setUseOutputWalkers($resultSet ? $resultSet->getUseOutputWalkers() : null);

		return $paginatedQuery->count();
	}

	/**
	 * @param Queryable $repository
	 * @param int $hydrationMode
	 *
	 * @return ResultSet|array
	 */
	public function fetch(Queryable $repository, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
	{
		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(null);

		return $hydrationMode !== AbstractQuery::HYDRATE_OBJECT
			? $query->execute(null, $hydrationMode)
			: $this->lastResult;
	}

	/**
	 * If You encounter a problem with the LIMIT 1 here,
	 * you should instead of fetching toMany relations just use postFetch.
	 *
	 * And if you really really need to hack it, just override this method and remove the limit.
	 *
	 * @param Queryable $repository
	 * @return object
	 */
	public function fetchOne(Queryable $repository)
	{
		$query = $this->getQuery($repository)
			->setFirstResult(null)
			->setMaxResults(1);

		return $query->getSingleResult();
	}

	/**
	 * @param Queryable $repository
	 * @param \Iterator $iterator
	 * @return void
	 */
	public function postFetch(Queryable $repository, \Iterator $iterator)
	{
		$this->onPostFetch($this, $repository, $iterator);
	}

	/**
	 * @return \Doctrine\ORM\Query
	 * @internal For Debugging purposes only!
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}

	private function toQuery($query)
	{
		if ($query instanceof Doctrine\ORM\QueryBuilder) {
			$query = $query->getQuery();
		}

		if (!$query instanceof Doctrine\ORM\AbstractQuery) {
			throw new UnexpectedValueException(
				"Method " . get_called_class() . "::doCreateQuery must return " .
				"instanceof Doctrine\\ORM\\Query or Kdyby\\Doctrine\\QueryBuilder or Kdyby\\Doctrine\\DqlSelection, " .
				(is_object($query) ? 'instance of ' . get_class($query) : gettype($query)) . " given."
			);
		}

		return $query;
	}
}
