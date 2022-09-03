<?php

namespace App\Model\Brand\Managers;

use App\Core\Doctrine\EntityManager;
use App\Model\Brand\Entities\Brand;
use App\Model\Brand\Entities\BrandCategory;
use App\Model\Brand\Queries\BrandQuery;
use App\Model\Brand\Repositories\BrandRepository;
use Nette\SmartObject;

class BrandManager
{
	use SmartObject;

	/** @var EntityManager */
	private $em;

	/** @var BrandRepository */
	private $brandRepository;

	public function __construct(EntityManager $em, BrandRepository $brandRepository)
	{
		$this->em = $em;
		$this->brandRepository = $brandRepository;
	}

	public function createBrandQuery(): BrandQuery
    {
		return new BrandQuery();
	}

	public function fetch(BrandQuery $query)
	{
		return $this->brandRepository->fetch($query);
	}

	public function createBrand(string $title, string $description, BrandCategory $brandCategory): Brand
    {
		$brand = new Brand($title, $description, $brandCategory);

		return $this->saveBrand($brand);
	}

	public function saveBrand(Brand $brand): Brand
    {
		$this->em->persist($brand);
		$this->em->flush($brand);

		return $brand;
	}

    public function deleteBrand(Brand $brand): Brand
    {
        $this->em->remove($brand);
        $this->em->flush();

        return $brand;
    }
}
