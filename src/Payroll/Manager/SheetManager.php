<?php

namespace Payroll\Manager;

use Doctrine\ORM\EntityManager;
use Payroll\Entity\Item;
use Payroll\Entity\Sheet;
use Symfony\Component\Console\Output\OutputInterface;

class SheetManager
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

    public function handleSheet(OutputInterface $output, string $sheetName, bool $delete)
    {
        $sheetRepository = $this->entityManager->getRepository(Sheet::class);
        /** @var Sheet $sheet */
        $sheet = $sheetRepository->findOneByName($sheetName);

        // Если опция удалить задана
        if ($delete) {
            // Если указаный лист найден
            if ($sheet) {
                // Удаляем его
                $this->entityManager->remove($sheet);
                $this->entityManager->flush();
                // Говорим, что удалили лист
                $output->writeln("Лист <fg=red>{$sheet->getName()}</> удален.");
            }
            // Если указаный лист не найден
            else {
                // Говорим, что указаный лист не найден
                $output->writeln("Лист <fg=blue>{$sheetName}</> не найден.");
            }
        }
        // Если опция удалить НЕ задана
        else {
            // Находим активный лист
            /** @var Sheet $activeSheet */
            $activeSheet = $sheetRepository->findOneByActive(true);
            // Если указаны лист найден
            if ($sheet) {
                // Если найденый лист активен
                if ($sheet->isActive()) {
                    // Говорим, что мы уже на этом листе
                    $output->writeln("Уже на листе <fg=green>{$sheet->getName()}</>.");
                }
                // Если указаный лист НЕ активен
                else {
                    // В активном листе есть активные итемы
                    /** @var Item $activeSheetOpenItem */
                    if ($activeSheet && $activeSheetOpenItem = $this->entityManager->getRepository(Item::class)->getSheetOpenItem($activeSheet)) {
                        // Закрываем активную задачу активного итема
                        $activeSheetOpenItem->setEndDate(new \DateTime());
                        // Строковое представление только что закрытой активной задачи активного листа
                        $activeSheetOpenItemStringRepresentation = $activeSheetOpenItem->getNote() ?? '#'.$activeSheetOpenItem->getId();
                        // Сообщаем что закрыли активную задачу активного листа
                        $output->writeln("Активная задача <fg=blue>{$activeSheetOpenItemStringRepresentation}</> листа <fg=blue>{$activeSheet->getName()}</> закрыта.");
                    }
                    // Если есть активный лист
                    if ($activeSheet) {
                        // Делаем активный лист неактивным
                        $activeSheet->setActive(false);
                        // Сообщаем, что сделали неактивным активный лист
                        $output->writeln("Лист <fg=blue>{$activeSheet->getName()}</> более не активен.");
                    }
                    // Делаем активным указаный лист
                    $sheet->setActive(true);
                    $this->entityManager->flush();
                    // Говорим, какой лист сделали активным
                    $output->writeln("Теперь активен лист <fg=green>{$sheet->getName()}</>.");
                }
            }
            // Если указаный лист НЕ найден
            else {
                // В активном листе есть активные итемы
                /** @var Item $activeSheetOpenItem */
                if ($activeSheet && $activeSheetOpenItem = $this->entityManager->getRepository(Item::class)->getSheetOpenItem($activeSheet)) {
                    // Закрываем активную задачу активного итема
                    $activeSheetOpenItem->setEndDate(new \DateTime());
                    // Строковое представление только что закрытой активной задачи активного листа
                    $activeSheetOpenItemStringRepresentation = $activeSheetOpenItem->getNote() ?? '#'.$activeSheetOpenItem->getId();
                    // Сообщаем, что задача активного итема закрыта
                    $output->writeln("Активная задача <fg=blue>{$activeSheetOpenItemStringRepresentation}</> листа <fg=green>{$activeSheet->getName()}</> закрыта.");
                }
                // Если есть активный лист
                if ($activeSheet) {
                    // Делаем активный лист неактивным
                    $activeSheet->setActive(false);
                    // Сообщаем, что сделали неактивным активный лист
                    $output->writeln("Лист <fg=blue>{$activeSheet->getName()}</> более не активен.");
                }
                // Создаем новый лист и делаем его активным
                $newSheet = (new Sheet())->setName($sheetName)->setActive(true);
                $this->entityManager->persist($newSheet);
                $this->entityManager->flush();
                // Пишем, что создали новый лист
                $output->writeln("Создан новый лист <fg=cyan>{$newSheet->getName()}</>.");
            }
        }
    }

    public function listSheets(OutputInterface $output)
    {
        $sheets = $this->entityManager->getRepository(Sheet::class)->findAll();
        if (count($sheets) == 0) {
            $output->writeln('Ни одного листа не найдено.');
        }
        /** @var Sheet $sheet */
        foreach ($sheets as $sheet) {
            if ($sheet->isActive()) {
                $output->writeln('<fg=green>'.$sheet->getName().'</>');
            } else {
                $output->writeln($sheet->getName());
            }
        }
    }
}
