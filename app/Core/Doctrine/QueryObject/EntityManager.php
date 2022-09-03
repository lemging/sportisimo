<?php

declare(strict_types=1);

namespace App\Core\Doctrine\QueryObject;

use Nettrine\ORM\EntityManagerDecorator;

class EntityManager extends EntityManagerDecorator
{
	/**
	 * @param string $entityName
	 * @return QueryObjectRepository
	 */
	public function getRepository($entityName): QueryObjectRepository
	{
		/** @var QueryObjectRepository $repository */
		$repository = parent::getRepository($entityName);
		return $repository;
	}
}
