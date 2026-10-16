<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/provider_config.php';
require_once __DIR__ . '/providers/ProductProviderInterface.php';
require_once __DIR__ . '/providers/BaseProvider.php';
require_once __DIR__ . '/providers/AmazonNinjaProvider.php';
require_once __DIR__ . '/providers/EbayProvider.php';
require_once __DIR__ . '/providers/DummyJsonProvider.php';
require_once __DIR__ . '/providers/FakeStoreProvider.php';
require_once __DIR__ . '/providers/MockProvider.php';
require_once __DIR__ . '/../api/recommendation.php';
require_once __DIR__ . '/exchange_rate.php';

/**
 * Normalizes and converts URL string inputs into search keywords.
 */
function extractKeywordFromUrl($url) {
    if (empty($url)) return '';
    if (!filter_var($url, FILTER_VALIDATE_URL)) return $url;

    $path_query = parse_url($url, PHP_URL_PATH) . ' ' . parse_url($url, PHP_URL_QUERY);
    $clean_keywords = preg_replace('/[^\w]/', ' ', $path_query);
    $words = array_filter(explode(' ', strtolower($clean_keywords)));

    // Exclude common URL/filler keywords
    $exclude = ['http', 'https', 'www', 'com', 'org', 'net', 'dp', 'gp', 'product', 'item', 'search', 'query', 's', 'k', '_nkw'];
    $filtered = array_diff($words, $exclude);

    if (empty($filtered)) {
        return 'electronics';
    }

    return implode(' ', array_slice($filtered, 0, 4));
}

/**
 * Orchestrator: Queries all active API providers, merges duplicates,
 * calculates recommendation scores, saves to the database, and returns results.
 */
function searchProvidersAndStore($query, $isStrict = true) {
    global $ENABLED_PROVIDERS, $pdo;
    
    // Normalize text search
    $searchQuery = trim($query);
    if (filter_var($searchQuery, FILTER_VALIDATE_URL)) {
        $searchQuery = extractKeywordFromUrl($searchQuery);
    }

    if (empty($searchQuery)) {
        return [];
    }

    $exchangeRate = getUsdToInrRate();
    $rawProducts = [];

    // 1. Gather products from enabled providers
    foreach ($ENABLED_PROVIDERS as $providerClass) {
        if (class_exists($providerClass)) {
            try {
                $provider = new $providerClass();
                $results = $provider->search($searchQuery);
                if (is_array($results)) {
                    // Convert foreign currency (USD) to INR using dynamic rate if not already in INR
                    foreach ($results as &$res) {
                        if (!isset($res['currency']) || $res['currency'] !== 'INR') {
                            if (isset($res['current_price'])) {
                                $res['current_price'] = round($res['current_price'] * $exchangeRate, 2);
                            }
                            if (isset($res['original_price'])) {
                                $res['original_price'] = round($res['original_price'] * $exchangeRate, 2);
                            }
                        }
                    }
                    unset($res);
                    $rawProducts = array_merge($rawProducts, $results);
                }
            } catch (Exception $e) {
                // Log provider failure silently to DB logs and proceed
                error_log("Provider $providerClass failed on search '$searchQuery': " . $e->getMessage());
                try {
                    $stmt = $pdo->prepare("INSERT INTO provider_logs (provider_name, status, response_time_ms, error_message) VALUES (?, 500, 0, ?)");
                    $stmt->execute([$providerClass, $e->getMessage()]);
                } catch (PDOException $dbEx) {
                    error_log("Database logging failed: " . $dbEx->getMessage());
                }
            }
        }
    }

    // Filter out accessory noise, dummy test data, and obsolete legacy products
    $filteredRaw = [];
    $qLower = strtolower($searchQuery);
    $qWords = array_values(array_filter(explode(' ', $qLower)));

    // Detect if user specifically asked for an accessory
    $userWantsAccessory = false;
    $accessoryTriggers = [
        'case', 'cover', 'protector', 'charger', 'charging', 'cable', 'cord', 'wire', 
        'battery', 'lamp', 'light', 'stand', 'holder', 'mount', 'strap', 'adapter', 
        'dock', 'hub', 'accessory', 'accessories'
    ];
    foreach ($accessoryTriggers as $atw) {
        if (strpos($qLower, $atw) !== false) {
            $userWantsAccessory = true;
            break;
        }
    }

    $accessoryKeywords = [
        'case', 'cover', 'protector', 'screen guard', 'tempered glass', 'strap', 'sleeve', 
        'pouch', 'shell', 'holster', 'lamp', 'light', 'ring light', 'selfie lamp', 'selfie light',
        'charger', 'charging', 'cable', 'cord', 'wire', 'battery pack', 'power bank', 'magsafe battery',
        'holder', 'stand', 'mount', 'tripod', 'gimbal', 'adapter', 'dock', 'hub', 'dongle',
        'cleaner', 'cloth', 'sticker', 'skin', 'decal', 'grip', 'popsocket', 'replacement',
        'repair', 'spare parts', 'spare', 'parts', 'lens protector', 'camera protector', 'dummy', 'toy'
    ];

    foreach ($rawProducts as $prod) {
        $title = strtolower($prod['name'] ?? '');
        $category = strtolower($prod['category'] ?? '');
        $isNoise = false;

        // 1. Strict accessory filter when searching for primary device
        if (!$userWantsAccessory) {
            if (strpos($category, 'accessor') !== false || strpos($category, 'mobile-accessories') !== false) {
                $isNoise = true;
            }
            if (!$isNoise) {
                foreach ($accessoryKeywords as $akw) {
                    if (preg_match('/\b' . preg_quote($akw, '/') . '\b/i', $title)) {
                        $isNoise = true;
                        break;
                    }
                }
            }
            if (!$isNoise && preg_match('/\b(compatible with|compatible for|designed for|for iphone|for samsung|for macbook|for ipad)\b/i', $title)) {
                $isNoise = true;
            }
        }

        // 2. Filter sandbox/dummy/test listings
        if (!$isNoise && preg_match('/\b(test|sample|dummy|prototype|sealed test)\b/i', $title) && !preg_match('/\b(test|sample)\b/i', $qLower)) {
            $isNoise = true;
        }

        // 3. Obsolete smartphone generations (e.g. iPhone 3/4/5/6/7/8 when user only searched general "iphone")
        if (!$isNoise && $qLower === 'iphone') {
            if (preg_match('/\biphone\s*(3|4|5|5s|5c|6|6s|6 plus|7|7 plus|8|8 plus)\b/i', $title)) {
                $isNoise = true;
            }
        }

        // 4. Require query keyword relevance in product name
        if (!$isNoise) {
            foreach ($qWords as $qw) {
                if (strlen($qw) >= 3 && strpos($title, $qw) === false && strpos($category, $qw) === false) {
                    $isNoise = true;
                    break;
                }
            }
        }

        if (!$isNoise) {
            $filteredRaw[] = $prod;
        }
    }

    $rawProducts = $filteredRaw;

    // 2. Fallback / Augment with high-fidelity flagship items if filtered set has fewer than 4 items
    if (count($rawProducts) < 4) {
        try {
            $mock = new MockProvider();
            $mockResults = $mock->search($searchQuery);
            if (is_array($mockResults)) {
                // Convert USD prices to INR using dynamic rate
                foreach ($mockResults as &$res) {
                    if (isset($res['current_price'])) {
                        $res['current_price'] = round($res['current_price'] * $exchangeRate, 2);
                    }
                    if (isset($res['original_price'])) {
                        $res['original_price'] = round($res['original_price'] * $exchangeRate, 2);
                    }
                }
                unset($res);
                $rawProducts = array_merge($rawProducts, $mockResults);
            }
        } catch (Exception $e) {
            error_log("MockProvider augmentation failed: " . $e->getMessage());
        }
    }

    // 3. Deduplicate / Merge products
    $mergedProducts = [];
    foreach ($rawProducts as $product) {
        $cleanName = strtolower(preg_replace('/[^\w]/', '', $product['name']));
        $key = $product['website'] . '_' . strtolower($product['brand']) . '_' . $cleanName;

        if (isset($mergedProducts[$key])) {
            if ($product['current_price'] < $mergedProducts[$key]['current_price']) {
                $mergedProducts[$key] = $product;
            }
        } else {
            $mergedProducts[$key] = $product;
        }
    }
    $products = array_values($mergedProducts);

    if (empty($products)) {
        return [];
    }

    // 4. Persist to Database & Log Price History
    $savedProducts = [];
    foreach ($products as $prod) {
        $dbId = saveOrUpdateProduct($prod);
        $stmt = $pdo->prepare("SELECT * FROM product_prices WHERE id = ?");
        $stmt->execute([$dbId]);
        $dbProd = $stmt->fetch();
        if ($dbProd) {
            $savedProducts[] = $dbProd;
        }
    }

    // 5. Run Recommendation Scoring with Prominence & Quality Weighting
    $prices = array_column($savedProducts, 'current_price');
    $minPrice = $prices ? min($prices) : 1.0;

    foreach ($savedProducts as &$item) {
        $score_data = getDetailedRecommendationScore(
            (float)$item['current_price'],
            (float)$item['rating'],
            (int)$item['review_count'],
            $minPrice,
            (int)$item['discount_rate'],
            (bool)$item['is_out_of_stock']
        );
        $score = $score_data['score'];

        // Brand & query prominence boost
        $nameLower = strtolower($item['product_name']);
        if (strpos($nameLower, $qLower) === 0 || strpos($nameLower, 'apple ' . $qLower) === 0) {
            $score += 6.0;
        }

        // High rating & verified review volume boost
        if ($item['rating'] >= 4.5 && $item['review_count'] >= 100) {
            $score += 4.0;
        }

        $item['score'] = min(99.0, max(10.0, round($score, 1)));
        $item['score_reasons'] = $score_data['reasons'];
    }
    unset($item);

    // Sort by highest score descending
    usort($savedProducts, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    // Curate results: Return top 5 proper products (as requested)
    return array_slice($savedProducts, 0, 5);
}

/**
 * Saves a normalized product array into the MySQL database, updates metadata if existing,
 * and logs historic price changes.
 */
function saveOrUpdateProduct($product) {
    global $pdo;

    // Search existing product by name and website
    $stmt = $pdo->prepare("SELECT id, current_price FROM product_prices WHERE product_name = ? AND website = ?");
    $stmt->execute([$product['name'], $product['website']]);
    $existing = $stmt->fetch();

    if ($existing) {
        $product_id = $existing['id'];
        $old_price = (float)$existing['current_price'];
        $new_price = (float)$product['current_price'];

        // Update details
        $update = $pdo->prepare("
            UPDATE product_prices 
            SET current_price = ?, original_price = ?, discount_rate = ?, rating = ?, review_count = ?, 
                image_url = ?, description = ?, is_out_of_stock = ?, category = ?,
                seller_name = ?, item_condition = ?, item_id = ?, shipping_info = ?
            WHERE id = ?
        ");
        $update->execute([
            $product['current_price'],
            $product['original_price'],
            $product['discount_rate'],
            $product['rating'],
            $product['review_count'],
            $product['image_url'],
            $product['description'],
            $product['availability'] === 0 ? 1 : 0, // is_out_of_stock mapping
            $product['category'],
            $product['seller_name'] ?? null,
            $product['item_condition'] ?? null,
            $product['item_id'] ?? null,
            $product['shipping_info'] ?? null,
            $product_id
        ]);

        // Log to price history if price changed
        if (abs($old_price - $new_price) > 0.01) {
            $hist = $pdo->prepare("INSERT INTO price_history (product_id, price) VALUES (?, ?)");
            $hist->execute([$product_id, $new_price]);

            // Check for real-time price drop alerts
            if ($new_price < $old_price) {
                try {
                    require_once __DIR__ . '/mailer.php';
                    $stmtAlert = $pdo->prepare("
                        SELECT t.id as tracker_id, t.target_price, u.id as user_id, u.email, u.full_name, 
                               p.product_name, p.product_url
                        FROM product_trackers t
                        JOIN users u ON t.user_id = u.id
                        JOIN product_prices p ON t.product_id = p.id
                        WHERE t.product_id = ? AND t.is_active = 1 AND ? <= t.target_price AND u.alerts_enabled = 1
                    ");
                    $stmtAlert->execute([$product_id, $new_price]);
                    $triggeredAlerts = $stmtAlert->fetchAll();

                    foreach ($triggeredAlerts as $alert) {
                        sendPriceAlertEmail(
                            $alert['email'],
                            $alert['full_name'],
                            $alert['product_name'],
                            $old_price,
                            $new_price,
                            $alert['product_url']
                        );

                        // Insert database notification
                        $stmtNotif = $pdo->prepare("INSERT INTO user_notifications (user_id, title, message, product_id) VALUES (?, ?, ?, ?)");
                        $stmtNotif->execute([
                            $alert['user_id'],
                            'Price Drop Alert',
                            $alert['product_name'] . ' is now down to ₹' . number_format($new_price, 2),
                            $product_id
                        ]);
                        
                        // Deactivate alert after sending
                        $updateAlert = $pdo->prepare("UPDATE product_trackers SET is_active = 0 WHERE id = ?");
                        $updateAlert->execute([$alert['tracker_id']]);
                    }
                } catch (Exception $alertEx) {
                    error_log("Real-time alert checking failed: " . $alertEx->getMessage());
                }
            }
        }
    } else {
        // Insert new product
        $insert = $pdo->prepare("
            INSERT INTO product_prices 
            (product_name, website, current_price, original_price, discount_rate, rating, review_count, image_url, product_url, description, is_out_of_stock, category, seller_name, item_condition, item_id, shipping_info) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([
            $product['name'],
            $product['website'],
            $product['current_price'],
            $product['original_price'],
            $product['discount_rate'],
            $product['rating'],
            $product['review_count'],
            $product['image_url'],
            $product['product_url'],
            $product['description'],
            $product['availability'] === 0 ? 1 : 0,
            $product['category'],
            $product['seller_name'] ?? null,
            $product['item_condition'] ?? null,
            $product['item_id'] ?? null,
            $product['shipping_info'] ?? null
        ]);
        
        $product_id = $pdo->lastInsertId();

        // Insert initial history timeline
        seedProductPriceHistory($pdo, $product_id, (float)$product['current_price'], (float)$product['original_price'], (float)$product['discount_rate']);
    }

    return $product_id;
}

// Deprecated compat functions for old scraper calls
function scrapeProductUrl($url) {
    $results = searchProvidersAndStore($url);
    return !empty($results) ? $results[0] : null;
}
