<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class Entity
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function isPersisted(): bool
    {
        return $this->id !== null;
    }
}
