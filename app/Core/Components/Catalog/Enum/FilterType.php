<?php
/**
 * @package  Types.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Enum;

use App\Core\Components\Catalog\Dto\Filter\Type\Input;
use App\Core\Components\Catalog\Dto\Filter\Type\PerPage;
use App\Core\Components\Catalog\Dto\Filter\Type\Select;

enum FilterType: string
{

    case input = 'input';

    case select = 'select';

    case perpage = 'perpage';

    public function get_type_class(): string {
        return match ($this) {
            self::input => Input::class,
            self::select => Select::class,
            self::perpage => PerPage::class,
        };
    }

}