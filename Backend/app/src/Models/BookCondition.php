<?php
namespace App\Models;

enum BookCondition: string {
    case New = 'New';
    case Good = 'Good';
    case Fair = 'Fair';
    case Poor = 'Poor';
    case Unknown = 'Unknown';

    /**
     * Create from value, handling both new and legacy database values
     */
    public static function fromValue(string $value): self {
        // Try direct match first
        $result = self::tryFrom($value);
        if ($result !== null) {
            return $result;
        }

        // Handle legacy database values
        return match($value) {
            'Like New' => self::New,
            'Gently Used' => self::Good,
            'Used' => self::Fair,
            'Heavily Used' => self::Poor,
            default => self::Unknown,
        };
    }

    public function label(): string {
    return match($this) {
        self::New => 'Like New',
        self::Good => 'Gently Used',
        self::Fair => 'Used',
        self::Poor => 'Heavily Used',
        self::Unknown => 'Unknown',
    };
    }
}