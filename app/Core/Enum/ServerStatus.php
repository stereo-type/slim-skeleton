<?php
/**
 * @package  ServerStatus.php
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Enum;

enum ServerStatus: int
{

    case SUCCESS = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    /**Например, при получении кешированных данных*/
    case NON_AUTHORITATIVE_INFORMATION = 203;

    case REDIRECT = 302;

    case VALIDATION_ERROR = 422;
    case TO_MANY_REQUESTS = 429;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND_REQUEST= 404;

    case SERVER_ERROR = 500;
    case BAD_GATEWAY = 502;

}