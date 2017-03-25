<?php

namespace Payroll\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sheet")
 * @ORM\Entity(repositoryClass="Payroll\Repository\SheetRepository")
 */
class Sheet
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="active", type="boolean")
     *
     * @var bool
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Payroll\Entity\Item", mappedBy="sheet", cascade={"remove"})
     *
     * @var Item[]
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Sheet
     */
    public function setName(string $name): Sheet
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Sheet
     */
    public function setActive(bool $active): Sheet
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param Item $item
     * @return Sheet
     */
    public function addItem(Item $item): Sheet
    {
        $this->items->add($item);
        return $this;
    }

    /**
     * @param Item $item
     * @return Sheet
     */
    public function removeItem(Item $item): Sheet
    {
        $this->items->removeElement($item);
        return $this;
    }
}
