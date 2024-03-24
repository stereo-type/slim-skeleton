<?php

namespace App\Core\Enum;

enum  PermissionCondition: string
{
    case all = 'all';
    case any = 'any';
}