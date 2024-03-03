<?php
/**
 * @package  Types.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Enum;

use InvalidArgumentException;
use Stevebauman\Purify\Facades\Purify;

enum ParamType: string
{

    case paramInt = 'int';

    case paramText = 'text';

    case paramRaw = 'raw';

    case paramBool = 'bool';

    case paramFloat = 'float';

    public function clean($data): mixed
    {
        if (is_object($data)) {
            throw new InvalidArgumentException('data can\'t be object');
        }
        if (is_array($data)) {
            throw new InvalidArgumentException('data can\'t be array');
        }
        $cleaned = Purify::clean((string)$data);
        return match ($this) {
            self::paramRaw => $cleaned,
            self::paramInt => (int)$cleaned,
            self::paramText => strip_tags($cleaned),
            self::paramBool => (bool)$cleaned,
            self::paramFloat => (float)$cleaned,
        };
    }

}