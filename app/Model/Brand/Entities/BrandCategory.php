<?php declare(strict_types = 1);

namespace App\Model\Brand\Entities;

use App\Model\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Brand\Repositories\BrandCategoryRepository")
 * @ORM\Table(name="brand_category")
 */
class BrandCategory extends Entity
{
    /**
     * @ORM\Column(type="string")
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @ORM\OneToMany(targetEntity="\App\Model\Brand\Entities\Brand", mappedBy="brandCategory")
     * @var Collection<int, Brand>
     */
    private Collection $brands;

    public function __construct(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;
        $this->brands = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
