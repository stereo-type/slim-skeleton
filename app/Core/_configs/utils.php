<?php
/**
 * @package  utils.php
 * @copyright 03.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

/**
 * @param  string  $directory
 * @param  string  $subpath
 * @return string[]
 */
function getPathsRecursively(string $directory, string $subpath): array
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $result = [];
    foreach ($iterator as $path) {
        if ($path->isDir()) {
            $pathArray = explode('/', $path->getPathname());
            if (strtolower(end($pathArray)) === strtolower($subpath)) {
                $result[] = $path->getPathname();
            }
        }
    }
    return $result;
}
