<?php

namespace App\Enums;

enum StoreRole: string
{
    case OWNER = 'eier';
    case ADMIN = 'admin';
    case STAFF = 'medarbeider';
}
