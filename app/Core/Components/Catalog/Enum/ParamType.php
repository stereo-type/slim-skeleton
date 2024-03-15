<?php
/**
 * @package  Types.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Enum;

use App\Core\Container;
use App\Core\Services\Purifier;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

enum ParamType: string
{

    case PARAM_INT = 'int';

    case PARAM_TEXT = 'text';

    case PARAM_RAW = 'raw';

    case PARAM_BOOL = 'bool';

    case PARAM_FLOAT = 'float';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function clean($data): mixed
    {
        if (is_object($data)) {
            throw new InvalidArgumentException('data can\'t be object');
        }
        if (is_array($data)) {
            throw new InvalidArgumentException('data can\'t be array');
        }

        $purifier = Container::get_container()->get(Purifier::class);
        $cleaned = $purifier->purify(trim((string)$data));
        return match ($this) {
            self::PARAM_RAW => $cleaned,
            self::PARAM_INT => (int)$cleaned,
            self::PARAM_TEXT => strip_tags($cleaned),
            self::PARAM_BOOL => (bool)$cleaned,
            self::PARAM_FLOAT => (float)$cleaned,
        };
    }

}