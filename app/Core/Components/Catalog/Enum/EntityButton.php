<?php

namespace App\Core\Components\Catalog\Enum;

enum EntityButton: string
{
    case create = 'create';

    case edit = 'edit';

    case copy = 'copy';

    case delete = 'delete';


    public function icon(): string
    {
        return match ($this) {
            self::create => 'bi bi-file-earmark-plus',
            self::edit => 'bi bi-pencil-square',
            self::copy => 'bi bi-copy',
            self::delete => 'bi bi-trash3',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::create => 'var(--bs-blue)',
            self::edit => 'var(--bs-cyan)',
            self::copy => 'var(--bs-orange)',
            self::delete => 'var(--bs-pink)',
        };
    }

    public function button_class(): string
    {
        return match ($this) {
            self::create => 'text-primary-emphasis bg-primary-subtle border-primary-subtle',
            self::edit => 'text-info-emphasis bg-info-subtle border-info-subtle',
            self::copy => 'text-warning-emphasis bg-warning-subtle border-warning-subtle',
            self::delete => 'text-danger-emphasis bg-danger-subtle border-danger-subtle',
        };
    }

    public function toMap(): array
    {
        return [
            'name' => $this->name,
            'icon' => $this->icon(),
            'button_class' => $this->button_class(),
        ];
    }

}