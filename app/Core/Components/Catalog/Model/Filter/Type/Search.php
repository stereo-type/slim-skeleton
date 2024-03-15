<?php
/**
 * @package  Input.php
 * @copyright 25.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Model\Filter\Type;

use App\Core\Container;
use App\Core\Services\Translator;
use App\Core\Components\Catalog\Model\Table\Collections\Attributes;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Search extends Filter
{

    public const IGNORE_IN_FILTER_REQUEST = true;

    public function render(): string
    {
        return "<button type=\"submit\" $this->attributes>" . $this->get_value() . "</button>";
    }

    /**
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function build(): self
    {
        $translator = Container::get_container()->get(Translator::class);

        return new self(
            'submit',
            Attributes::fromArray(['class' => 'btn text-primary-emphasis bg-primary-subtle border-primary-subtle p-1', 'style' => 'min-width: 50px; grid-column: 12;']),
            $translator->translate('search')
        );
    }

}