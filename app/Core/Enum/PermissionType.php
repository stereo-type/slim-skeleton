<?php

namespace App\Core\Enum;

enum  PermissionType: string
{
    case read = 'read';
    case write = 'write';
}