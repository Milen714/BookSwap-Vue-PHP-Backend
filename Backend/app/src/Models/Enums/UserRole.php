<?php
namespace App\Models\Enums;
enum UserRole: string{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case GUEST = 'GUEST';

    
}