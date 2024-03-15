<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

class Input extends Filter
{

    private function _type(): string
    {
        return $this->attributes['type'] ?? 'text';
    }

    public function render(): string
    {
        $type = $this->_type();
        return "<input type=\"$type\" name=\"$this->name\" $this->attributes value=\"" . $this->get_value() . "\">";
    }
}