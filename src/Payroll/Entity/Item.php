<?php

namespace Payroll\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="Payroll\Repository\ItemRepository")
 */
class Item
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
     * @ORM\Column(name="start_date", type="datetime")
     *
     * @var \DateTime
     */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $endDate;

    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     *
     * @var string
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Payroll\Entity\Sheet", inversedBy="items")
     * @ORM\JoinColumn(name="sheet_id", referencedColumnName="id")
     *
     * @var Sheet
     */
    private $sheet;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return Item
     */
    public function setStartDate(\DateTime $startDate): Item
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return Item
     */
    public function setEndDate(\DateTime $endDate): Item
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return Item
     */
    public function setNote(string $note): Item
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return Sheet
     */
    public function getSheet(): Sheet
    {
        return $this->sheet;
    }

    /**
     * @param Sheet $sheet
     * @return Item
     */
    public function setSheet(Sheet $sheet): Item
    {
        $this->sheet = $sheet;
        return $this;
    }
}
