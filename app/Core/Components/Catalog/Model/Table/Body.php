<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Model\Table\Collections\Rows;

readonly class Body
{
    public function __construct(
        public Rows $rows,
        public Attributes $attributes = new Attributes(),
    ) {
    }

    public function toMap(): array
    {
        return [
            'attributes' => $this->attributes->toMap(),
            'rows'       => $this->rows->toMap(),
        ];
    }

}