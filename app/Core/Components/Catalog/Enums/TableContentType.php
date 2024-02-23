<?php
/**
 * @package  TableContentType.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Enums;

enum TableContentType: string
{

    case html = 'html';

    case json = 'json';

}