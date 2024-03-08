<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use App\Core\Components\Catalog\Model\Table\Attribute;
use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\ParamType;

class Space extends Filter
{

    public const IGNORE_IN_FILTER_REQUEST = true;

    public function __construct(
        string    $name,
        iterable  $attributes = new Attributes(),
        mixed     $defaultValue = null,
        iterable  $params = [],
        int       $length = self::DEFAULT_LENGTH,
        ParamType $paramType = ParamType::PARAM_RAW
    )
    {
        parent::__construct($name, $attributes, $defaultValue, $params, $length, $paramType);
        $this->attributes->remove('class');
        $this->attributes->add(new Attribute('class', "length-$this->length"));
    }

    public function render(): string
    {
        return "<div $this->attributes>" . $this->get_value() . "</div>";
    }


}