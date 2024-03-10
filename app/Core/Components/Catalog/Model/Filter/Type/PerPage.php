<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\ParamType;

 class PerPage extends Select
{

    public static function build(): self
    {
        return new self(
            'perpage',
            Attributes::fromArray(['style' => 'grid-column: 10;']),
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