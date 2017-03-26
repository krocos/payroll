<?php

namespace Payroll\Manager;

use Doctrine\ORM\EntityManager;
use Payroll\Entity\Item;
use Payroll\Entity\Sheet;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
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
                        // Сообщаем что закрыли активную задачу активного листа
                        $output->writeln("Активная задача <fg=blue>{$activeSheetOpenItem}</> листа <fg=blue>{$activeSheet->getName()}</> закрыта.");
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
                    // Сообщаем, что задача активного итема закрыта
                    $output->writeln("Активная задача <fg=blue>{$activeSheetOpenItem}</> листа <fg=green>{$activeSheet->getName()}</> закрыта.");
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
            return;
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

    public function display(OutputInterface $output, float $hourlyRate = null, bool $itemsWithId)
    {
        /** @var Sheet $sheet */
        $sheet = $this->entityManager->getRepository(Sheet::class)->findOneByActive(true);
        if (!$sheet) {
            $output->writeln("Ни один лист не активен на данный момент.");
            return;
        }
        /** @var Item[] $items */
        $items = $this->entityManager->getRepository(Item::class)->findBy(['sheet' => $sheet], ['startDate' => 'ASC']);

        $output->writeln('');
        $output->writeln('Лист: '.$sheet->getName());
        $output->writeln('');

        $table = new Table($output);

        $headers = [];

        if ($itemsWithId) {
            $headers[] = '#';
        }
        $headers[] = 'День';
        $headers[] = 'Начало';
        $headers[] = 'Конец';
        $headers[] = 'Длит-ть';
        if ($hourlyRate) {
            $headers[] = 'Стоимость';
        }
        $headers[] = 'Заметка';

        $table->setHeaders($headers);

        $rows = [];
        $previousDay = null;
        $diffAddDate = new \DateTime();
        $diffDate = clone $diffAddDate;
        $totalHourlyRate = 0.0;
        /** @var Item $item */
        foreach ($items as $item) {
            $row = [];

            // #
            if ($itemsWithId) {
                $row[] = $item->getId();
            }

            // День
            $day = $item->getStartDate()->format('j M Y');
            if (is_null($previousDay) || $previousDay != $day) {
                $previousDay = $day;
                $row[] = $day;
            } else {
                $row[] = '';
            }

            // Начало
            $row[] = $item->getStartDate()->format('H:i');

            // Конец
            if (!is_null($item->getEndDate())) {
                $row[] = $item->getEndDate()->format('H:i');
            } else {
                $row[] = "--";
            }

            // Длительность
            if (!is_null($item->getEndDate())) {
                $endDate = $item->getEndDate();
            } else {
                $endDate = new \DateTime();
            }
            $interval = $endDate->diff($item->getStartDate());
            $d = (int)$interval->format('%a');
            $h = $interval->format('%h');
            $m = $interval->format('%I');
            $row[] = ($d > 0 ? $d . 'д ' : ' ').$h.':'.$m;

            // Добавляем этот интервал для дальнейшего расчета
            $diffAddDate->add($interval);

            // Стоимость
            if ($hourlyRate) {
                $intervalInSeconds = $endDate->getTimestamp() - $item->getStartDate()->getTimestamp();
                $hours = ($intervalInSeconds / 60) / 60;
                $computedRate = $hours * $hourlyRate;
                $row[] = sprintf('%.02f', round($computedRate, 2));
                $totalHourlyRate += $computedRate;
            }

            // Заметка
            $row[] = $item->getNote() ?? '';

            // Добавляем строку
            $rows[] = $row;
        }

        $rows[] = new TableSeparator();

        $totalInterval = $diffAddDate->diff($diffDate);
        $d = (int)$totalInterval->format('%a');
        $h = $totalInterval->format('%h');
        $m = $totalInterval->format('%I');
        $totalTime = ($d > 0 ? $d . 'д ' : ' ').$h.':'.$m;

        $row = [];
        if ($itemsWithId) {
            $row[] = '';
        }
        $row[] = '';
        $row[] = '';
        $row[] = '';
        $row[] = $totalTime;

        if ($hourlyRate) {
            $row[] = sprintf('%.02f', round($totalHourlyRate, 2));
        }

        $rows[] = $row;

        $table->addRows($rows);

        // Выравнивание в ячейках
        $durationIndex = 3;
        $billIndex = 4;
        if ($itemsWithId) {
            $table->setColumnStyle(0, (new TableStyle())->setPadType(STR_PAD_LEFT));
            $durationIndex = 4;
            $billIndex = 5;
        }
        $table->setColumnStyle($durationIndex, (new TableStyle())->setPadType(STR_PAD_LEFT));
        if ($hourlyRate) {
            $table->setColumnStyle($billIndex, (new TableStyle())->setPadType(STR_PAD_LEFT));
        }

        $table->render();

        $output->writeln('');
    }
}
