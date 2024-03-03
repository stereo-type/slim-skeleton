<?php
/**
 * @package  TranslationExtension.php
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Core\Services\Translator;

class TranslationExtension extends AbstractExtension
{

    public function __construct(private readonly Translator $translator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('translate', [$this, 'translate']),
        ];
    }

    public function translate($key): string
    {
        return $this->translator->translate($key);
    }
}