<?php
/**
 * @package  TableHeader.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;

readonly class Cell
{
    public function __construct(
        public string $data,
        public Attributes $attributes = new Attributes(),
        public CellParams $params = new CellParams()
    ) {
    }

    public function render(): string
    {
        $tag = $this->params->header ? 'th' : 'td';
        return "<$tag $this->attributes $this->params>$this->data</$tag>";
    }

}