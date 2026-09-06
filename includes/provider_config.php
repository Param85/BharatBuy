<?php
/**
 * Configuration settings for external product providers and caches.
 */

define('PROVIDER_CACHE_DURATION', 1800); // 30 minutes in seconds

// List of active provider classes to call in search
$ENABLED_PROVIDERS = [
    'AmazonNinjaProvider',
    'EbayProvider',
    'DummyJsonProvider',
    'FakeStoreProvider'
];

// Provider-specific API Credentials
$PROVIDER_CREDENTIALS = [
    'AmazonNinjaProvider' => [
        'api_url' => 'https://api.openwebninja.com/realtime-amazon-data/search',
      'api_key' => $_ENV['AMAZON_NINJA_API_KEY'] ?? $_SERVER['AMAZON_NINJA_API_KEY'] ?? getenv('AMAZON_NINJA_API_KEY') ?: ''
    ],
    'EbayProvider' => [
        'client_id' => $_ENV['EBAY_CLIENT_ID'] ?? $_SERVER['EBAY_CLIENT_ID'] ?? getenv('EBAY_CLIENT_ID') ?: '', 
        'client_secret' => $_ENV['EBAY_CLIENT_SECRET'] ?? $_SERVER['EBAY_CLIENT_SECRET'] ?? getenv('EBAY_CLIENT_SECRET') ?: '',
        'dev_id' => $_ENV['EBAY_DEV_ID'] ?? $_SERVER['EBAY_DEV_ID'] ?? getenv('EBAY_DEV_ID') ?: '',
        'sandbox' => true, // Set to false for Production
        'oauth_url' => 'https://api.sandbox.ebay.com/identity/v1/oauth2/token',
        'api_url' => 'https://api.sandbox.ebay.com/buy/browse/v1/item_summary/search'
    ],
    'DummyJsonProvider' => [
        'api_url' => 'https://dummyjson.com/products/search'
    ],
    'FakeStoreProvider' => [
        'api_url' => 'https://fakestoreapi.com/products'
    ]
];
