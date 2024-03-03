<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter\Type;

use App\Core\Components\Catalog\Dto\Table\Attribute;
use InvalidArgumentException;
use App\Core\Components\Catalog\Dto\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\ParamType;

readonly class Select extends Filter
{
    private array $options;

    /**
     * @param  string  $name
     * @param  ParamType  $type
     * @param  Attributes  $attributes
     * @param  mixed|null  $defaultValue
     * @param  iterable  $params
     * @param  int  $length
     */
    public function __construct(
        string $name,
        ParamType $type,
        iterable $attributes = new Attributes(),
        mixed $defaultValue = null,
        iterable $params = [],
        int $length = self::DEFAULT_LENGTH
    ) {
        if (!isset($params['options'])) {
            throw new InvalidArgumentException('Select options must be specified');
        }

        $this->options = $params['options'];
        parent::__construct($name, $type, $attributes, $defaultValue, $params, $length);
    }

    public function render(): string
    {
        $html = "<select  name=\"$this->name\" $this->attributes>";
        foreach ($this->options as $key => $value) {
            $html .= "<option value=\"$key\"";
            if ($this->defaultValue === $key) {
                $html .= " selected=\"selected\"";
            }
            $html .= '>';
            $html .= $value;
            $html .= '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}