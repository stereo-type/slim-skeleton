<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use InvalidArgumentException;
use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\ParamType;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Select extends Filter
{
    private array $options;

    public const FORM_CONTROL = false;

    /**
     * @param string $name
     * @param Attributes $attributes
     * @param mixed|null $defaultValue
     * @param iterable $params
     * @param int $length
     * @param ParamType $paramType
     */
    public function __construct(
        string $name,
        iterable $attributes = new Attributes(),
        mixed $defaultValue = null,
        iterable $params = [],
        int $length = self::DEFAULT_LENGTH,
        ParamType $paramType = ParamType::PARAM_INT
    ) {
        if (!isset($params['options'])) {
            throw new InvalidArgumentException('Select options must be specified');
        }

        $this->options = $params['options'];

        $attributes = Attributes::mergeAttributes(
            Attributes::MERGE_JOIN,
            Attributes::fromArray($attributes),
            Attributes::fromArray(['class' => 'form-select']),
        );

        parent::__construct($name, $attributes, $defaultValue, $params, $length, $paramType);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        $_value = $this->paramType->clean($this->get_value());
        $html = "<select  name=\"$this->name\" $this->attributes>";
        foreach ($this->options as $key => $value) {
            $html .= "<option value=\"$key\"";
            if ($_value === $this->paramType->clean($key)) {
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