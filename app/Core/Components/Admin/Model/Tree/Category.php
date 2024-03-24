<?php
/**
 * @package  Category.php
 * @copyright 24.03.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Admin\Model\Tree;


use App\Core\Components\Admin\Model\Tree\Collections\CategoryItems;

readonly class Category
{


    public function __construct(public CategoryHead $head, public CategoryItems $items)
    {
    }
}