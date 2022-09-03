<?php

namespace App\Core\Doctrine\QueryObject\Exceptions;

use Doctrine\ORM\AbstractQuery;

class QueryException extends \RuntimeException
{
	/** @var AbstractQuery|null */
	public $query;

	public function __construct($previous, ?AbstractQuery $query = null, string $message = "")
	{
		parent::__construct($message ?: $previous->getMessage(), 0, $previous);

		$this->query = $query;
	}
}
