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

    // Filter out accessory noise and model mismatches
    $filteredRaw = [];
    if (!$isStrict) {
        $filteredRaw = $rawProducts;
    } else {
        // Extract specific model search terms from query to ensure model accuracy
        $qWords = array_filter(explode(' ', strtolower($searchQuery)));
        $genericTerms = ['phone', 'mobile', 'smartphone', 'unlocked', '5g', '4g', 'lte', 'gsm', 'dual', 'sim', 'international', 'version', 'model', 'with', 'for'];
        
        // Model words are words in search query excluding the first word (usually brand) and generic terms
        $modelWords = [];
        if (count($qWords) > 1) {
            $brand = array_shift($qWords); // remove first word (brand)
            foreach ($qWords as $word) {
                $wordClean = preg_replace('/[^\w]/', '', $word);
                if (strlen($wordClean) >= 2 && !in_array($wordClean, $genericTerms)) {
                    $modelWords[] = $wordClean;
                }
            }
        }

        foreach ($rawProducts as $prod) {
            $q = strtolower($searchQuery);
            $title = strtolower($prod['name']);
            $isNoise = false;
            
            // 1. Accessory filter
            $accessoryKeywords = ['case', 'cover', 'protector', 'screen guard', 'tempered glass', 'strap', 'sleeve', 'pouch', 'shell', 'holster'];
            foreach ($accessoryKeywords as $word) {
                if (strpos($title, $word) !== false && strpos($q, $word) === false) {
                    $isNoise = true;
                    break;
                }
            }
            
            if (strpos($title, 'compatible with') !== false && strpos($q, 'compatible') === false) {
                $isNoise = true;
            }
            if (strpos($title, 'compatible for') !== false && strpos($q, 'compatible') === false) {
                $isNoise = true;
            }

            // 2. Specific model matching filter
            if (!$isNoise && !empty($modelWords)) {
                $hasModelMatch = false;
                foreach ($modelWords as $mWord) {
                    if (strpos($title, $mWord) !== false) {
                        $hasModelMatch = true;
                        break;
                    }
                }
                if (!$hasModelMatch) {
                    $isNoise = true; // Discard completely different model search spillover
                }
            }

            if (!$isNoise) {
                $filteredRaw[] = $prod;
            }
        }
    }
    $rawProducts = $filteredRaw;

    // 2. Fallback to MockProvider if all external APIs returned empty sets
    if (empty($rawProducts)) {
        try {
            $mock = new MockProvider();
            $results = $mock->search($searchQuery);
            if (is_array($results)) {
                // Convert USD prices to INR using dynamic rate
                foreach ($results as &$res) {
                    if (isset($res['current_price'])) {
                        $res['current_price'] = round($res['current_price'] * $exchangeRate, 2);
                    }
                    if (isset($res['original_price'])) {
                        $res['original_price'] = round($res['original_price'] * $exchangeRate, 2);
                    }
                }
                unset($res);
                $rawProducts = $results;
            }
        } catch (Exception $e) {
            error_log("MockProvider fallback failed: " . $e->getMessage());
        }
    }

    // 3. Deduplicate / Merge products
    $mergedProducts = [];
    foreach ($rawProducts as $product) {
        // Build a unique duplicate check key based on website, brand, and normalized title
        $cleanName = strtolower(preg_replace('/[^\w]/', '', $product['name']));
        $key = $product['website'] . '_' . strtolower($product['brand']) . '_' . $cleanName;

        if (isset($mergedProducts[$key])) {
            // Merge duplicates by keeping the cheaper deal
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

    // 5. Run Recommendation Scoring (calculated relative to lowest compared price)
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
        $item['score'] = $score_data['score'];
        $item['score_reasons'] = $score_data['reasons'];
    }
    unset($item);

    // Sort by highest score descending
    usort($savedProducts, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return $savedProducts;
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
                            '🔥 Price Drop Alert!',
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
