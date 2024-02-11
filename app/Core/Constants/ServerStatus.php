<?php
/**
 * @package  ServerStatus.php
 * @copyright 10.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Constants;

class ServerStatus
{

    public const REDIRECT = 302;
    public const VALIDATION_ERROR = 422;
    public const TO_MANY_REQUESTS = 429;
    public const BAD_REQUEST = 400;
    public const NOT_FOUND_REQUEST = 404;

}