<?php
/**
 * Recommendation Engine Scoring Logic
 * 
 * Calculates a product recommendation score based on:
 * - 40% Price score (Relative to the cheapest option)
 * - 30% Rating score (Relative to 5 stars)
 * - 30% Popularity score (Based on review count capped at 1000 reviews)
 *
 * @param float $price The price of the current product.
 * @param float $rating The product rating (0 to 5).
 * @param int $reviews The number of reviews for the product.
 * @param float $min_price The minimum price among compared products.
 * @return float Score rounded to 1 decimal place (out of 100).
 */
function getRecommendationScore($price, $rating, $reviews, $min_price) {
    // Prevent division by zero or invalid negative prices
    if ($price <= 0) {
        return 0.0;
    }
    
    // 40% Price score (Relative to cheapest)
    $price_score = ($min_price / $price) * 40;
    
    // 30% Rating score (Out of 5 stars)
    $rating_score = (max(0, min($rating, 5)) / 5) * 30;
    
    // 30% Popularity score (Capped at 1000 reviews)
    $review_score = (max(0, min($reviews, 1000)) / 1000) * 30;
    
    return round($price_score + $rating_score + $review_score, 1);
}

/**
 * Calculates a detailed product recommendation score along with list of explanation reasons.
 *
 * @param float $price The price of the current product.
 * @param float $rating The product rating (0 to 5).
 * @param int $reviews The number of reviews for the product.
 * @param float $min_price The minimum price among compared products.
 * @param int $discount_rate Original discount percentage (if any).
 * @param bool $is_out_of_stock Stock status.
 * @return array Array with keys 'score' (float) and 'reasons' (array of strings).
 */
function getDetailedRecommendationScore($price, $rating, $reviews, $min_price, $discount_rate = 0, $is_out_of_stock = false) {
    if ($price <= 0) {
        return [
            'score' => 0.0,
            'reasons' => ['Invalid product price.']
        ];
    }

    $reasons = [];

    // 1. Price Score (40% weight relative to cheapest compared price)
    $price_ratio = $min_price / $price;
    $price_score = $price_ratio * 40;
    if ($price_ratio >= 0.98) {
        $reasons[] = "Cheapest option among compared deals.";
    } elseif ($price_ratio >= 0.90) {
        $reasons[] = "Highly competitive price (within 10% of lowest deal).";
    } elseif ($price_ratio >= 0.80) {
        $reasons[] = "Reasonably priced compared to alternative options.";
    } else {
        $reasons[] = "Priced higher than other similar offers.";
    }

    // 2. Rating Score (30% weight out of 5 stars)
    $rating_score = (max(0, min($rating, 5)) / 5) * 30;
    if ($rating >= 4.5) {
        $reasons[] = "Highly rated by customers ($rating/5 stars).";
    } elseif ($rating >= 3.5) {
        $reasons[] = "Decent customer rating of $rating/5 stars.";
    } else {
        $reasons[] = "Lower customer rating of $rating/5 stars.";
    }

    // 3. Popularity Score (30% weight capped at 1000 reviews)
    $review_score = (max(0, min($reviews, 1000)) / 1000) * 30;
    if ($reviews >= 500) {
        $reasons[] = "Extremely popular with over " . number_format($reviews) . " reviews.";
    } elseif ($reviews >= 100) {
        $reasons[] = "Good review count with " . number_format($reviews) . " comments.";
    } else {
        $reasons[] = "Few customer reviews (only " . number_format($reviews) . ").";
    }

    // 4. Discount Rate bonus
    if ($discount_rate >= 15) {
        $reasons[] = "Significant discount of $discount_rate% off the original price.";
    }

    // 5. Stock Status penalty
    if ($is_out_of_stock) {
        $reasons[] = "Warning: Product is currently out of stock.";
        $total_score = max(0, ($price_score + $rating_score + $review_score) - 30);
    } else {
        $total_score = $price_score + $rating_score + $review_score;
    }

    return [
        'score' => round($total_score, 1),
        'reasons' => $reasons
    ];
}

/**
 * Fetches price statistics (lowest, highest, average) for a given product ID.
 *
 * @param int $product_id
 * @return array Array with keys 'lowest', 'highest', and 'average'.
 */
function getProductPriceStats($product_id) {
    global $pdo;

    // Fetch the current price from product_prices
    $stmt = $pdo->prepare("SELECT current_price FROM product_prices WHERE id = ?");
    $stmt->execute([$product_id]);
    $current = (float)$stmt->fetchColumn();

    // Fetch the min, max, average from price_history
    $stmt = $pdo->prepare("SELECT MIN(price) as lowest, MAX(price) as highest, AVG(price) as average FROM price_history WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $stats = $stmt->fetch();

    $lowest = ($stats && $stats['lowest'] !== null) ? (float)$stats['lowest'] : $current;
    $highest = ($stats && $stats['highest'] !== null) ? (float)$stats['highest'] : $current;
    $average = ($stats && $stats['average'] !== null) ? (float)$stats['average'] : $current;

    // Standardize min/max bounds relative to the current live price
    $lowest = min($lowest, $current);
    $highest = max($highest, $current);

    return [
        'lowest' => $lowest,
        'highest' => $highest,
        'average' => round($average, 2)
    ];
}

/**
 * Fetches similar products in the same category, ordered by rating, excluding the current product.
 *
 * @param int $product_id
 * @param string $category
 * @param int $limit
 * @return array
 */
function getSimilarProducts($product_id, $category, $limit = 4) {
    global $pdo;
    
    if (empty($category)) {
        return [];
    }

    $stmt = $pdo->prepare("SELECT id, product_name, website, current_price, rating, image_url FROM product_prices WHERE category = ? AND id != ? ORDER BY rating DESC LIMIT ?");
    $stmt->bindValue(1, $category, PDO::PARAM_STR);
    $stmt->bindValue(2, $product_id, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Seeds a realistic multi-point historical price timeline for a product if it lacks history.
 *
 * @param PDO $pdo
 * @param int $productId
 * @param float $currentPrice
 * @param float $originalPrice
 * @param float $discountRate
 * @return bool
 */
function seedProductPriceHistory($pdo, $productId, $currentPrice, $originalPrice = 0, $discountRate = 0) {
    if ($productId <= 0 || $currentPrice <= 0) {
        return false;
    }

    // Determine realistic reference high/list price
    $highPrice = (float)$originalPrice;
    if ($highPrice <= 0 || $highPrice <= $currentPrice) {
        if ($discountRate > 0 && $discountRate < 80) {
            $highPrice = $currentPrice / (1 - ($discountRate / 100));
        } else {
            $highPrice = $currentPrice * 1.15;
        }
    }

    // Delete existing sparse entries for clean sequence
    $del = $pdo->prepare("DELETE FROM price_history WHERE product_id = ?");
    $del->execute([$productId]);

    // Intervals in days before today: 45d, 32d, 21d, 14d, 5d, 0d (today)
    $intervals = [45, 32, 21, 14, 5, 0];

    // Seed-based stable randomness so repeated views generate consistent realistic points for same product
    mt_srand($productId * 37);

    $p0 = round($highPrice * (1 + (mt_rand(-2, 2) / 100)), 2);
    $p1 = round(($highPrice * 0.96) * (1 + (mt_rand(-2, 2) / 100)), 2);
    $p2 = round(($currentPrice * 1.06) * (1 + (mt_rand(-3, 3) / 100)), 2);
    $p3 = round(($highPrice * 0.98) * (1 + (mt_rand(-2, 2) / 100)), 2);
    $p4 = round(($currentPrice * 1.03) * (1 + (mt_rand(-1, 2) / 100)), 2);
    $p5 = $currentPrice;

    $values = [$p0, $p1, $p2, $p3, $p4, $p5];

    $insertStmt = $pdo->prepare("INSERT INTO price_history (product_id, price, recorded_at) VALUES (?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))");
    for ($i = 0; $i < count($intervals); $i++) {
        $insertStmt->execute([$productId, $values[$i], $intervals[$i]]);
    }

    mt_srand(); // Reset random generator

    return true;
}

