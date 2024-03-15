<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use App\Core\Components\Catalog\Model\Table\Collections\Attributes;

class Page extends Filter
{

    public const INIT_PAGE = 1;

    private function _type(): string
    {
        return $this->attributes['type'] ?? 'text';
    }

    public function render(): string
    {
        $type = $this->_type();
        return "<input type=\"$type\" name=\"$this->name\" $this->attributes value=\"" . $this->get_value() . "\">";
    }

    public static function build(): self
    {
        return new self('page', Attributes::fromArray(['class' => 'd-none']), self::INIT_PAGE, length: 0);
    }

}