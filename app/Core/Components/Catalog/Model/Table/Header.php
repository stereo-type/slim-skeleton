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

readonly class Header
{
    public Rows $rows;

    public function __construct(
        $rows = new Rows(),
        public Attributes $attributes = new Attributes(),
    ) {
        foreach ($rows->toArray() as $r) {
            foreach ($r->cells->toArray() as $c) {
                $c->attributes->add(new Attribute('scope', 'col'));
                $c->params->setHeader(true);
            }
        }
        $this->rows = $rows;
    }

    public function toMap(): array
    {
        return [
            'attributes' => $this->attributes->toMap(),
            'rows'       => $this->rows->toMap(),
        ];
    }

}