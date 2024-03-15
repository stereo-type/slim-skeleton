<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

class CellParams
{
    public function __construct(
        public readonly int $colspan = 1,
        public readonly int $rowspan = 1,
        public bool $header = false
    ) {
    }

    public function setHeader(bool $value): void
    {
        $this->header = $value;
    }

    public function toMap():array {
        return [
            'colspan' => $this->colspan,
            'rowspan' => $this->rowspan,
            'header' => $this->header,
        ];
    }

    public function __toString(): string
    {
        $params = [];
        if ($this->colspan > 1) {
            $params [] = "colspan=$this->colspan";
        }
        if ($this->rowspan > 1) {
            $params [] = "rowspan=$this->rowspan";
        }

        return implode(' ', $params);
    }


}