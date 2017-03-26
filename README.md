# Payroll

## About

Учет времени в терминале.

## Install

Экспортируем или в `.bashrc` или `.zshrc` переменную

`$ export COMPOSER_HOME=$HOME/.config/composer`

и изменяем `$PATH`

`$ export PATH=$COMPOSER_HOME/vendor/bin:$PATH`

Затем устанавливаем

`$ composer global require krocos/payroll:dev-master`

Затем можно использовать как

`$ payroll`

## Help

`sheet [-l] [-d] "<название листа>"`

(-d для удаления) (если есть опция -l то выводится список листов) переключение на лист, если листа нет — создает, если есть неостановленные задачи, останавливает и переключает

`edit <номер итема> —start "<date>" —end "<date>" [—note "<note here>" | (-a | --append) <append to note> ]`

опции `--note` и `--append` не могут использоваться вместе и последняя ставит `, ` в заметке и добавляет `<append to note>`

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
