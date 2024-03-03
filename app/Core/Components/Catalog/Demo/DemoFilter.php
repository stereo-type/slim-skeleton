<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;

use App\Core\Components\Catalog\Dto\Filter\Filters;
use App\Core\Components\Catalog\Dto\Filter\Type\Filter;
use App\Core\Components\Catalog\Enum\FilterType;
use App\Core\Components\Catalog\Enum\ParamType;
use App\Core\Components\Catalog\Providers\CatalogFilterInterface;

class DemoFilter implements CatalogFilterInterface
{
    public function filters(array $formData): Filters
    {
        $filters = [];
        $filters[] = Filter::create(FilterType::input, 'id', ParamType::paramInt, ['placeholder' => 'ID']);
        $filters[] = Filter::create(FilterType::input, 'name', ParamType::paramText);
        $filters[] = Filter::create(FilterType::input, 'description', ParamType::paramText, length: 4);
        $filters[] = Filter::create(FilterType::input, 'description2', ParamType::paramText);
        $filters[] = Filter::create(FilterType::input, 'description4', ParamType::paramText, length: 6);
        $filters[] = Filter::create(FilterType::input, 'description5', ParamType::paramText, length: 3);
        $filters[] = Filter::create(FilterType::input, 'description6', ParamType::paramText, length: 3);
        $filters[] = Filter::create(
            FilterType::select,
            'description7',
            ParamType::paramText,
            params: ['options' => ['12', '3', '23']]
        );
        return new Filters($filters);
    }
}