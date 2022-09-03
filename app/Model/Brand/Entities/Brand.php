<?php declare(strict_types = 1);

namespace App\Model\Brand\Entities;

use App\Model\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Model\Brand\Repositories\BrandRepository")
 * @ORM\Table(name="brand")
 */
class Brand extends Entity
{
    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Model\Brand\Entities\BrandCategory", inversedBy="brands")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private BrandCategory $category;

    public function __construct(string $title, string $description, BrandCategory $category)
    {
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): BrandCategory
    {
        return $this->category;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setCategory(BrandCategory $category): void
    {
        $this->category = $category;
    }
}
