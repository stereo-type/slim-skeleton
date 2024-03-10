<?php
/**
 * @package  TableDataProvider.php
 * @copyright 23.02.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Core\Components\Catalog\Demo;


use App\Core\Entity\User;
use App\Core\Components\Catalog\Providers\EntityDataProvider;

class DemoUserDataProvider extends EntityDataProvider
{

    public const ENTITY_CLASS = User::class;

    public function exclude_entity_properties(): array
    {
        return ['password'];
    }

    public function exclude_entity_filters(): array
    {
        return ['updatedAt', 'createdAt', 'verifiedAt'];
    }

    public function named_properties(): array
    {
        return [
            'twoFactor'  => '2FA',
            'name'       => "Имя",
            'verifiedAt' => 'Подтвержден',
            'createdAt'  => 'Создан',
            'updatedAt'  => 'Изменен',
            'id'         => 'ID',
            'email'      => 'E-mail'
        ];
    }

}