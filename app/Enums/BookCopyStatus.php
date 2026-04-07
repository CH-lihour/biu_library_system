<?php

namespace App\Enums;

enum BookCopyStatus: string
{
    case AVAILABLE = 'available';
    case BORROWED = 'borrowed';
    case DAMAGED = 'damaged';
    case LOST = 'lost';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
