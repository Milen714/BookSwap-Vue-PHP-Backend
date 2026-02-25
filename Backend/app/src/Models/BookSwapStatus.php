<?php

namespace App\Models;
enum BookSwapStatus: string {
    case PENDING = 'PENDING';
    case SHIPPINGPAID = 'SHIPPINGPAID';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case COMPLETED = 'COMPLETED';
    case TAKENDOWN = 'TAKENDOWN';
    case ALL = 'ALL';
}