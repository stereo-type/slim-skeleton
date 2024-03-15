<?php
/**
 * @package  AbstractFilter.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use RuntimeException;
use App\Core\Components\Catalog\Model\Table\Attribute;
use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\ParamType;

abstract class Filter
{

    readonly public Attributes $attributes;

    public const FILTER_PARAM_VALUE = 'value';
    public const FORM_CONTROL = true;

    public const DEFAULT_LENGTH = 2;
    /**Игнорировать ли данный фильтр при формировании запроса*/
    public const IGNORE_IN_FILTER_REQUEST = false;

    /**
     * @param string $name
     * @param Attributes $attributes
     * @param null $defaultValue
     * @param array $params
     * @param int $length
     * @param ParamType $paramType
     */
    public function __construct(
        readonly public string $name,
        iterable $attributes = new Attributes(),
        readonly public mixed $defaultValue = null,
        private iterable $params = [],
        readonly public int $length = self::DEFAULT_LENGTH,
        readonly public ParamType $paramType = ParamType::PARAM_TEXT,
    ) {
        /**Form element must have an accessible name: Element has no title attribute*/
        $attributes = Attributes::fromArray($attributes);
        if (!isset($attributes['title'])) {
            $attributes->add(new Attribute('title', $name));
        }

        $additional_class = "length-$this->length";
        if (static::FORM_CONTROL) {
            $additional_class .= ' form-control';
        }

        $this->attributes = Attributes::mergeAttributes(
            Attributes::MERGE_JOIN,
            Attributes::fromArray($attributes),
            Attributes::fromArray(['class' => $additional_class]),
        );
    }

    abstract public function render(): string;

    protected function placeholder(): ?string
    {
        return $this->attributes['placeholder'] ?? null;
    }


    public function __toString(): string
    {
        return $this->render();
    }

    public function get_params(): iterable
    {
        return $this->params;
    }

    public function set_param(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    public function get_value(): mixed
    {
        return $this->params[self::FILTER_PARAM_VALUE] ?? $this->defaultValue;
    }


    /**Метод для генерации фильтров по типу
     * @param FilterType $type
     * @param string $name
     * @param iterable $attributes - это атрибуты DOMElement фильтра
     * @param $defaultValue
     * @param iterable $params
     * @param int|null $length - длина фильтра в сетке n/12, по умолчанию 2
     * @param ParamType|null $paramType
     * @return Filter
     */
    public static function create(
        FilterType $type,
        string $name,
        iterable $attributes = [],
        $defaultValue = null,
        iterable $params = [],
        ?int $length = null,
        ?ParamType $paramType = ParamType::PARAM_TEXT,
    ): Filter {
        $class = $type->get_type_class();
        $instance = new $class(
            name: $name,
            attributes: Attributes::fromArray($attributes),
            defaultValue: $defaultValue,
            params: $params,
            length: $length ?? self::DEFAULT_LENGTH,
            paramType: $paramType
        );
        if ($instance instanceof self) {
            return $instance;
        }
        throw new RuntimeException('Incorrect instance ' . $class);
    }


}