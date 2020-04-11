<?php

declare(strict_types=1);

namespace App;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class DirectoriesBypass
{
    protected string $baseDir = '';
    protected int $minStubs = 0;
    protected int $maxStubs = 0;
    protected $result = null;
    protected ?array $errors = null;

    public function __construct(string $baseDir = 'example')
    {
        $this->baseDir = $baseDir;
        $this->setStubsRange(5, 20);
    }

    /**
     * Установка количественного интервала, в котором будут сгенерированы тестовые данные.
     *
     * @param null|int $minStubs
     * @param null|int $maxStubs
     */
    public function setStubsRange(?int $minStubs, ?int $maxStubs): void
    {
        $this->minStubs = $minStubs ?? $this->minStubs;
        $this->maxStubs = $maxStubs ?? $this->maxStubs;
    }

    /**
     * Возвращает текущее результируещее состояние.
     *
     * @return false|string
     */
    public function getResult()
    {
        return json_encode(
            [
                'data' => $this->result,
                'errors' => $this->errors,
            ],
            JSON_THROW_ON_ERROR,
            512
        );
    }

    /**
     * Создает дерево директорий и файлов для тестов.
     *
     * @throws Exception
     */
    public function makeStubs(): void
    {
        $count = 0;
        for ($i = 0, $j = random_int($this->minStubs, $this->maxStubs); $i < $j; $i++) {
            $path = $this->baseDir . '/' . str_repeat($i . 'stub/', $i);

            if (!file_exists($path) && !mkdir($path, 0644, true) && !is_dir($path)) {
                $this->errors[] = sprintf('Directory "%s" was not created.', $path);
            }

            if ($i % 3 === 1) {
                file_put_contents($path . '/count', 1);
                $count++;
            }
        }

        $this->result = sprintf('Created %d stub files.', $count);
    }

    /**
     * Получить сумму чисел в файлах. Вариант 1.
     */
    public function getSumFromFilesV1(): void
    {
        $dir = $this->baseDir;
        $dirs = [];
        $next = 0;
        $all = 0;
        $sum = 0;

        while (true) {
            $_dirs = glob($dir . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

            if (count($_dirs) > 0) {
                foreach ($_dirs as $_dir) {
                    $dirs[] = $_dir;
                    $all++;

                    if (file_exists($_dir . '/count')) {
                        $sum += (int)file_get_contents($_dir . '/count');
                    }
                }
            } elseif ($all > $next) {
                $dir = $dirs[$next++];
                continue;
            } else {
                break;
            }

            $dir = $dirs[$next++];
        }

        $this->result = $sum;
    }

    /**
     * Получить сумму чисел в файлах. Вариант 2.
     */
    public function getSumFromFilesV2(): void
    {
        $dir = $this->baseDir;
        $dirs = [];
        $next = 0;
        $all = 0;
        $sum = 0;

        while (true) {
            $items = scandir($dir);

            if (count($items) > 2) {
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }

                    $path = $dir . '/' . $item;
                    if (is_dir($path)) {
                        $dirs[] = $path;
                        $all++;
                    } elseif (is_file($path) && strpos($item, 'count') === 0) {
                        $sum += (int)file_get_contents($path);
                    }
                }
            } elseif ($all > $next) {
                $dir = $dirs[$next++];
                continue;
            } else {
                break;
            }

            $dir = $dirs[$next++];
        }

        $this->result = $sum;
    }

    /**
     * Получить сумму чисел в файлах. Вариант 3.
     */
    public function getSumFromFilesV3(): void
    {
        $directory = new RecursiveDirectoryIterator($this->baseDir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);
        $matches = new RegexIterator($iterator, '/^.+count$/i', RecursiveRegexIterator::GET_MATCH);
        $sum = 0;

        foreach ($matches as $path) {
            $sum += (int)file_get_contents($path[0]);
        }

        $this->result = $sum;
    }
}
