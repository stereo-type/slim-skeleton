<?php
/**
 * @package  Category.php
 * @copyright 15.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Table;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Model\Table\Collections\Rows;

readonly class Table
{

    public Attributes $attributes;

    public function __construct(
        public Body $body,
        public Header $header = new Header(),
        $attributes = new Attributes(),
    ) {
        $this->attributes = Attributes::mergeAttributes(
            Attributes::MERGE_JOIN,
            Attributes::fromArray($attributes),
            Attributes::fromArray(['class' => 'table table-hover table-striped']),
        );
    }

    /**Метод рендера таблицы
     * @param  Row[]  $rows
     * @param  array  $head
     * @param  iterable  $attributes
     * @return Table
     */
    public static function build(iterable $rows, iterable $head = [], iterable $attributes = []): Table
    {
        $headRows = [];
        if ($head instanceof Rows) {
            $headRows = $head;
        } else {
            $firstItem = reset($head);
            if (is_string($firstItem) || $firstItem instanceof Row) {
                $headRows = [$head];
            }
        }
        $body = new Body(Rows::fromArray($rows));
        $header = new Header(Rows::fromArray($headRows));
        $attr = Attributes::fromArray($attributes);
        return (new Table(body: $body, header: $header, attributes: $attr));
    }


    public function render(): string
    {
        $html = "<table $this->attributes>";
        if (!$this->header->rows->isEmpty()) {
            $head_attributes = (string)$this->header->attributes;
            $html .= "<thead $head_attributes>";
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

    public function toMap(): array
    {
        return [
            'structure' => [
                'attributes' => $this->attributes->toMap(),
                'header'     => $this->header->toMap(),
                'body'       => $this->body->toMap(),
            ],
            'table'  => $this->render(),
        ];
    }


}