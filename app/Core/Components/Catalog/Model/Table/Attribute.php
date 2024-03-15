<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

readonly class Attribute
{

    public function __construct(
        public string $key,
        public mixed $value
    ) {
    }

    public function __toString():string {
        $value = trim((string)$this->value);
        return "$this->key = \"$value\"";
    }
}