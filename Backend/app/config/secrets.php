<?php
namespace App\config;
// Keep your Stripe API key protected by including it as an environment variable
// or in a private script that does not publicly expose the source code.

// This is your test secret API key.
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: '';
$DOMAIN_URL = getenv('DOMAIN_URL') ?: 'http://localhost';
//$DOMAIN_URL = 'https://http://bookswap.art';
$BOOKS_API_KEY = getenv('BOOKS_API_KEY') ?: '';


class Secrets
{
    public const JWT_ALGORITHM = 'HS256';
    public static string $secretKey;
    public static string $domain;
    public static int $tokenExpirationHours;
    public static string $stripeSecretKey;
    public static string $stripePublicKey;
    public static string $stripeWebhookSecret;
    public static string $reCapchaSiteKey;
    public static string $reCapchaSecretKey;
    public static string $booksApiKey;

    public static function init(): void
    {
        self::$secretKey = getenv('JWT_SECRET_KEY') ?: $_ENV['JWT_SECRET_KEY'] ?? 'default_secret_key';
        self::$domain = getenv('DOMAIN_URL') ?: $_ENV['DOMAIN_URL'] ?? 'http://localhost';
        self::$tokenExpirationHours = (int)(getenv('TOKEN_EXPIRATION_HOURS') ?: $_ENV['TOKEN_EXPIRATION_HOURS'] ?? 24);
        self::$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: $_ENV['STRIPE_SECRET_KEY'] ?? '';
        self::$stripePublicKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
        self::$stripeWebhookSecret = getenv('STRIPE_WEBHOOK_SECRET') ?: $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';
        self::$reCapchaSiteKey = getenv('RECAPTCHA_SITE_KEY') ?: $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
        self::$reCapchaSecretKey = getenv('RECAPTCHA_SECRET_KEY') ?: $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
        self::$booksApiKey = getenv('BOOKS_API_KEY') ?: $_ENV['BOOKS_API_KEY'] ?? '';
    }
}

Secrets::init();