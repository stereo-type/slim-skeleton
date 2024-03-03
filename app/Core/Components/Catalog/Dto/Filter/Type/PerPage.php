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

readonly class PerPage extends Select
{

    public static function build(): self
    {
        return new self(
            'perpage',
            ParamType::paramInt,
            [],
            10,
            [
                'options' => [
                    10  => 10,
                    25  => 25,
                    100 => 100
                ]
            ]
        );
    }


}