<?php

namespace App\Core\Doctrine\QueryObject\Persistence;

interface Query
{
	public function count(Queryable $repository): int;

	/**
	 * @param Queryable $repository
	 * @return mixed
	 */
	public function fetch(Queryable $repository);

	/**
	 * @param Queryable $repository
	 * @return object
	 */
	public function fetchOne(Queryable $repository);
}
