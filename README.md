##### Установка зависимостей
`composer install`

##### Справка по консольным командам
`php console.php --help`

##### Генерация тестовых данных
`php console.php --command=makeStubs`

Необязательные параметры `--minStubs` и `--maxStubs`, по умолчанию 5 и 20

Выведет в консоль количество созданных файлов `count`, в которых содержится число 1

##### Вызов подсчета суммы. Вариант 1.
`php console.php --command=getSumFromFilesV1`

##### Вызов подсчета суммы. Вариант 2.
`php console.php --command=getSumFromFilesV2`

##### Вызов подсчета суммы. Вариант 3.
`php console.php --command=getSumFromFilesV3`

Сумма в консоли должна совпадать с количеством созданных файлов `count`.

Также выводятся метрики потребления памяти и времени.
