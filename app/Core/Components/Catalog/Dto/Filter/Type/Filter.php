<?php
/**
 * @package  AbstractFilter.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Dto\Filter\Type;

use App\Core\Components\Catalog\Dto\Table\Attribute;
use RuntimeException;
use App\Core\Components\Catalog\Dto\Table\Collections\Attributes;
use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\ParamType;

readonly abstract class Filter
{

    public Attributes $attributes;

    public const DEFAULT_LENGTH = 2;

    /**
     * @param  string  $name
     * @param  ParamType  $type
     * @param  Attributes  $attributes
     * @param  null  $defaultValue
     * @param  array  $params
     * @param  int  $length
     */
    public function __construct(
        public string $name,
        public ParamType $type,
        iterable $attributes = new Attributes(),
        public mixed $defaultValue = null,
        public iterable $params = [],
        public int $length = self::DEFAULT_LENGTH,
    ) {
        /**Form element must have an accessible name: Element has no title attribute*/
        $attributes = Attributes::fromArray($attributes);
        if (!isset($attributes['title'])) {
            $attributes->add(new Attribute('title', $name));
        }
        $this->attributes = Attributes::mergeAttributes(
            Attributes::MERGE_JOIN,
            Attributes::fromArray($attributes),
            Attributes::fromArray(['class' => "length-$this->length form-control"]),
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

    public static function create(
        FilterType $type,
        string $name,
        ParamType $paramType,
        iterable $attributes = [],
        $defaultValue = null,
        iterable $params = [],
        ?int $length = null
    ): Filter {
        $class = $type->get_type_class();
        $instance = new $class(
            name: $name,
            type: $paramType,
            attributes: Attributes::fromArray($attributes),
            defaultValue: $defaultValue,
            params: $params,
            length: $length ?? self::DEFAULT_LENGTH,
        );
        if ($instance instanceof self) {
            return $instance;
        }
        throw new RuntimeException('Incorrect instance '.$class);
    }


}