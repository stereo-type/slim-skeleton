<?php
/**
 * @package  Purifier.php
 * @copyright 04.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class Purifier
{

    private static ?HTMLPurifier $purifier = null;
    private HTMLPurifier $_purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $this->_purifier = new HTMLPurifier($config);
    }

    public static function build(): HTMLPurifier
    {
        if (!is_null(self::$purifier)) {
            return self::$purifier;
        }

        $purifier = new self();
        self::$purifier = $purifier->_purifier;
        return self::$purifier;
    }
}