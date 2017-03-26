<?php

namespace Payroll\Manager;

use Doctrine\ORM\EntityManager;
use Payroll\Entity\Item;
use Payroll\Entity\Sheet;
use Symfony\Component\Console\Output\OutputInterface;

class ItemManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function startItem(OutputInterface $output, string $note = null)
    {
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        // Нашли активный лист
        if ($sheet) {
            $itemRepository = $this->entityManager->getRepository(Item::class);
            /** @var Item $sheetOpenItem */
            $sheetOpenItem = $itemRepository->getSheetOpenItem($sheet);
            // Если в активном листе есть открытый интервал
            if ($sheetOpenItem) {
                // Говорим, что надо сначала закрыть предидущий интервал
                $output->writeln("На данный момент открыт интервал <fg=green>{$sheetOpenItem}</>");
            }
            // Если нет открытого интервала в активном листе
            else {
                // Создаем новый интервал
                $item = (new Item())
                    ->setStartDate(new \DateTime())
                    ->setNote($note)
                    ->setSheet($sheet);
                $this->entityManager->persist($item);
                $this->entityManager->flush();
                // Говорим, что открыли интервал
                $output->writeln('Время пошло!');
            }
        }
        // Активный лист не найден
        else {
            $output->writeln('Не выбран лист для создания интервала.');
        }
    }

    public function stopItem(OutputInterface $output)
    {
        /** @var Sheet|null $sheet */
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        if ($sheet) {
            /** @var Item $sheetOpenItem */
            $sheetOpenItem = $this->entityManager->getRepository(Item::class)->getSheetOpenItem($sheet);
            if ($sheetOpenItem) {
                $sheetOpenItem->setEndDate(new \DateTime());
                $this->entityManager->flush();
                // Говорим, что остановили интервал
                $output->writeln("Интервал <fg=green>{$sheetOpenItem}</> листа <fg=green>{$sheet->getName()}</> остановлен.");
            } else {
                $output->writeln("В листе <fg=green>{$sheet->getName()}</> не найден активный интервал.");
            }
        } else {
            $output->writeln('Не выбран лист в котором надо останавливать интервал.');
        }
    }

    public function now(OutputInterface $output)
    {
        /** @var Sheet|null $sheet */
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        if ($sheet) {
            /** @var Item $sheetOpenItem */
            $sheetOpenItem = $this->entityManager->getRepository(Item::class)->getSheetOpenItem($sheet);
            if ($sheetOpenItem) {
                $output->writeln("Выбран <fg=green>{$sheet->getName()}</> лист, идет время интервала <fg=green>{$sheetOpenItem}</> c <fg=magenta>{$sheetOpenItem->getStartDate()->format('Y-m-d H:i')}</>.");
            } else {
                $output->writeln("Выбран <fg=green>{$sheet->getName()}</> лист, время не идет.");
            }
        } else {
            $output->writeln('Лист не выбран.');
        }
    }

    public function addManualInterval(OutputInterface $output, string $startDate, string $endDate, string $note = null)
    {
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        if ($endDate < $startDate) {
            $output->writeln('Время начала интервала не может быть больше времени завершения.');
            return;
        }

        /** @var Sheet|null $sheet */
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        if ($sheet) {
            $item = (new Item())
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setNote($note)
                ->setSheet($sheet);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $output->writeln("Интервал <fg=cyan>{$item}</> добавлен в лист <fg=green>{$sheet->getName()}</>.");
        } else {
            $output->writeln('Лист не выбран.');
        }
    }

    public function delete(OutputInterface $output, int $inputId)
    {
        /** @var Sheet|null $sheet */
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        if ($sheet) {
            $item = $this->entityManager->getRepository(Item::class)->find($inputId);
            if ($item) {
                $this->entityManager->remove($item);
                $this->entityManager->flush();
                $output->writeln("Интервал <fg=blue>{$item}</> удален.");
            } else {
                $output->writeln("Интервал для удаления не найден в листе <fg=green>{$sheet->getName()}</>.");
            }
        } else {
            $output->writeln('Лист не выбран.');
        }
    }
}
