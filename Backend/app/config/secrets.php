<?php
// Keep your Stripe API key protected by including it as an environment variable
// or in a private script that does not publicly expose the source code.

// This is your test secret API key.
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: '';
$DOMAIN_URL = getenv('DOMAIN_URL') ?: 'http://localhost';
//$DOMAIN_URL = 'https://http://bookswap.art';
$BOOKS_API_KEY = getenv('BOOKS_API_KEY') ?: '';