<?php
/**
 * @package  Translator.php
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Services;

readonly class Translator
{
    private string $language;
    private array $translations;

    public function __construct($language)
    {
        $this->language = $language;
        $this->loadTranslations();
    }

    private function loadTranslations(): void
    {
        // Загружаем переводы из соответствующего файла
        $translationFile = LANG_PATH."/$this->language.php";
        if (file_exists($translationFile)) {
            $this->translations = include($translationFile);
        } else {
            // В случае отсутствия файла используем язык по умолчанию
            $this->translations = include(LANG_PATH.'/en.php');
        }
    }

    public function translate($key): string
    {
        return $this->translations[$key] ?? '[['.$key.']]';
    }
}
