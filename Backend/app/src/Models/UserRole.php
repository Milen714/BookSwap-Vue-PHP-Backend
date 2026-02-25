<?php
namespace App\Models;
enum UserRole: string{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case GUEST = 'GUEST';

    
}