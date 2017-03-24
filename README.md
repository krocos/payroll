`sheet "<название листа>"` — переключение на лист, если листа нет — создает, если есть неостановленные задачи, останавливает и переключает

`edit (-i | —item) <номер> —start "<date>" —end "<date>" —note "<note here>"`

`delete (-i | —item) <номер>` — удаляет запись из sheet

`display —start "<date>" —end "<date>" (-f |—format) <(json|xml|csv)> —timestamps (-r | —hourlyrate) <hourly rate number> —id` — записи из sheet в разных форматах, если опция —id предоставлена, показывать id записей

`manual <start date> <end date> —note "<note here>"` — создает мануальное время

`now` — показывает какая задача сейчас идет

`start` — запускает учет времени

`stop` — останавливает текущаую задачу