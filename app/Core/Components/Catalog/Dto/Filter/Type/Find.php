<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter\Type;

use App\Core\Components\Catalog\Dto\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\ParamType;
use App\Core\Services\Translator;

readonly class Find extends Filter
{

    public function render(): string
    {
        return "<button type=\"submit\" $this->attributes>$this->defaultValue</button>";
//        return "<button type=\"submit\" class=\"btn btn-primary\"
//                    style=\"min-width: 70px; grid-column: 12;\">{{ translate('search') }}</button>";
    }

    public static function build(): self
    {
        $container = include ROOT_PATH.'/bootstrap.php';
        $translator = $container->get(Translator::class);

        return new self(
            'submit',
            ParamType::paramBool,
            Attributes::fromArray(['class' => 'btn btn-primary', 'style' => 'min-width: 70px; grid-column: 12;']),
            $translator->translate('search')
        );
    }

}