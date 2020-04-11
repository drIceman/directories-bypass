<?php

declare(strict_types=1);

use App\DirectoriesBypass;

require __DIR__ . '/vendor/autoload.php';

set_exception_handler(
    static function (Throwable $ex) {
        fwrite(STDERR, $ex->getMessage() . PHP_EOL . 'file ' . $ex->getFile() . ' in line ' . $ex->getLine() . PHP_EOL);
    }
);

$options = getopt('', ['help::', 'command:', 'minStubs::', 'maxStubs::']);

if (isset($options['help'])) {
    echo <<<'HELP'
Доступные команды:
--makeStubs Создает дерево директорий и файлов для тестов.
  необязательные параметры:
  --minStubs Минимальное количество тестовых директорий, по умолчанию 5.
  --maxStubs Максимальное количество тестовых директорий, по умолчанию 20.
--getSumFromFilesV1 Получить сумму чисел в файлах. Вариант 1.
--getSumFromFilesV2 Получить сумму чисел в файлах. Вариант 2.
--getSumFromFilesV3 Получить сумму чисел в файлах. Вариант 3.
--help Вывод справки.

HELP;
    exit;
}

$allowedCommands = [
    'makeStubs',
    'getSumFromFilesV1',
    'getSumFromFilesV2',
    'getSumFromFilesV3',
];

if (!$options || !($command = $options['command']) || !in_array($command, $allowedCommands, true)) {
    throw new ErrorException('Missing or invalid required parameters.');
}

$mu = -memory_get_usage();
$tu = -hrtime(true);

$app = new DirectoriesBypass();
$app->setStubsRange((int)$options['minStubs'], (int)$options['maxStubs']);

$app->$command();

echo $app->getResult(), PHP_EOL;

echo 'Memory usage(Kb): ', ($mu + memory_get_usage()) / 1024, ' Time usage(ms): ', ($tu + hrtime(true)) / 1e+6, PHP_EOL;
