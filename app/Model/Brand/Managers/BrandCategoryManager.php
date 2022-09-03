<?php

namespace App\Model\Brand\Managers;

use App\Core\Doctrine\EntityManager;
use App\Model\Brand\Entities\BrandCategory;
use App\Model\Brand\Queries\BrandQuery;
use App\Model\Brand\Repositories\BrandCategoryRepository;
use Nette\SmartObject;

class BrandCategoryManager
{
	use SmartObject;

	/** @var EntityManager */
	private $em;

	/** @var BrandCategoryRepository */
	private $brandCategoryRepository;

	public function __construct(EntityManager $em, BrandCategoryRepository $brandCategoryRepository)
	{
		$this->em = $em;
		$this->brandCategoryRepository = $brandCategoryRepository;
	}

	public function fetch(BrandQuery $query)
	{
		return $this->brandCategoryRepository->fetch($query);
	}

	public function createBrandCategory(string $title, string $description)
	{
		$brandCategory = new BrandCategory($title, $description);

		return $this->saveBrandCategory($brandCategory);
	}

	public function saveBrandCategory(BrandCategory $brandCategory)
	{
		$this->em->persist($brandCategory);
		$this->em->flush($brandCategory);

		return $brandCategory;
	}
}
