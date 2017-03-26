# Payroll

## About

Учет времени в терминале.

## Install

`$ composer global require krocos/payroll:dev-master`

Затем можно использовать как

`$ payroll`

если добавить в `$PATH` путь к глобальной `bin` директории композера.

## Help

`sheet [-l] [-d] "<название листа>"`

(-d для удаления) (если есть опция -l то выводится список листов) переключение на лист, если листа нет — создает, если есть неостановленные задачи, останавливает и переключает

`edit <номер итема> —start "<date>" —end "<date>" —note "<note here>"`

`delete <номер итема>`

удаляет запись из sheet

`display [-r | —hourlyrate <hourly rate float>] [—id]`

записи из sheet, если опция —id предоставлена, показывать id записей

`manual <start date> <end date> [—note "<note here>"]`

создает мануальное время

`now`

показывает какая задача сейчас идет

`start [--note <note>]`

запускает учет времени

`stop`

останавливает текущаую задачу

## TODO

- Translations for interface and the readme.
