<?php

namespace Payroll\Repository;

use Doctrine\ORM\EntityRepository;
use Payroll\Entity\Item;
use Payroll\Entity\Sheet;

class ItemRepository extends EntityRepository
{
    public function getSheetOpenItem(Sheet $sheet): ?Item
    {
        return $this->findOneBy(['sheet' => $sheet, 'endDate' => null]);
    }
}
