<?php
/**
 * @package  Table.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto;

use App\Core\Components\Catalog\Dto\Collections\Attributes;
use App\Core\Components\Catalog\Dto\Collections\Rows;

readonly class Table
{
    public function __construct(
        public Body $body,
        public Header $header = new Header(),
        public Attributes $attributes = new Attributes(),
    ) {
    }


    public function render(): string
    {
        $html = "<table $this->attributes>";
        if (!$this->header->rows->isEmpty()) {
            $html .= '<thead>';
            foreach ($this->header->rows->toArray() as $row) {
                $html .= $row->render();
            }
            $html .= '</thead>';
        }
        $html .= '<tbody>';
        foreach ($this->body->rows->toArray() as $row) {
            $html .= $row->render();
        }
        $html .= '</tbody>';
        $html .= '</table>';
        return $html;
    }


    /**
     * @param  Row[]  $rows
     * @param  array  $head
     * @param  iterable  $attributes
     * @return string
     */
    public static function build(iterable $rows, iterable $head = [], iterable $attributes = []): string
    {
        $body = new Body(Rows::fromArray($rows));
        $header = new Header(Rows::fromArray([$head]));
        $attr = Attributes::fromArray($attributes);
        return (new Table(body: $body, header: $header, attributes: $attr))->render();
    }

}