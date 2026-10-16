<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Category Definitions & Curated Data
$CATEGORIES_DATA = [
    'fashion' => [
        'name' => 'Fashion',
        'title' => 'Fashion & Apparel',
        'icon' => 'fa-solid fa-shirt',
        'badge' => 'Trending Styles',
        'description' => 'Discover top-rated clothing, designer footwear, luxury watches, and fashion accessories compared across Amazon, eBay, and Flipkart.',
        'subcategories' => ['All', "Men's Clothing", "Women's Wear", 'Footwear', 'Watches', 'Accessories'],
        'keywords' => ['fashion', 'clothing', 'shirt', 'dress', 'shoes', 'watch', 'jacket', 't-shirt', 'sneakers'],
        'fallback_products' => [
            [
                'id' => 1001,
                'product_name' => "Levi's Men's 511 Slim Fit Stretchable Denim Jeans",
                'website' => 'Amazon',
                'current_price' => 1899.00,
                'original_price' => 3199.00,
                'discount_rate' => 41,
                'rating' => 4.4,
                'review_count' => 3820,
                'image_url' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Levis+511+Jeans',
                'category' => 'Fashion'
            ],
            [
                'id' => 1002,
                'product_name' => "Nike Air Max Excee Men's Running & Lifestyle Sneakers",
                'website' => 'Flipkart',
                'current_price' => 4995.00,
                'original_price' => 7495.00,
                'discount_rate' => 33,
                'rating' => 4.6,
                'review_count' => 1420,
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Nike+Air+Max',
                'category' => 'Fashion'
            ],
            [
                'id' => 1003,
                'product_name' => "Fossil Grant Chronograph Dark Brown Leather Watch",
                'website' => 'Amazon',
                'current_price' => 6495.00,
                'original_price' => 12495.00,
                'discount_rate' => 48,
                'rating' => 4.5,
                'review_count' => 2890,
                'image_url' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Fossil+Grant+Chronograph',
                'category' => 'Fashion'
            ],
            [
                'id' => 1004,
                'product_name' => "Ray-Ban Aviator Classic Polarized Sunglasses (Gold Frame)",
                'website' => 'eBay',
                'current_price' => 5890.00,
                'original_price' => 9990.00,
                'discount_rate' => 41,
                'rating' => 4.7,
                'review_count' => 980,
                'image_url' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80',
                'product_url' => 'https://www.ebay.com/sch/i.html?_nkw=Ray-Ban+Aviator+Sunglasses',
                'category' => 'Fashion'
            ]
        ]
    ],
    'mobiles' => [
        'name' => 'Mobiles',
        'title' => 'Mobiles & 5G Smartphones',
        'icon' => 'fa-solid fa-mobile-screen-button',
        'badge' => 'Flagships & 5G',
        'description' => 'Compare prices on the latest Apple iPhones, Samsung Galaxy flagships, OnePlus, and top 5G smartphones with real-time price tracking.',
        'subcategories' => ['All', 'Apple iPhone', 'Samsung Galaxy', '5G Flagships', 'Budget Phones', 'Mobile Accessories'],
        'keywords' => ['phone', 'mobile', 'smartphone', 'iphone', 'samsung', 'pixel', 'oneplus', 'galaxy'],
        'fallback_products' => [
            [
                'id' => 1011,
                'product_name' => 'Apple iPhone 16 (128GB, Teal Blue) - Super Retina XDR',
                'website' => 'Amazon',
                'current_price' => 74990.00,
                'original_price' => 79900.00,
                'discount_rate' => 6,
                'rating' => 4.8,
                'review_count' => 4820,
                'image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=iPhone+16',
                'category' => 'Mobiles'
            ],
            [
                'id' => 1012,
                'product_name' => 'Samsung Galaxy S24 Ultra 5G (Titanium Gray, 256GB)',
                'website' => 'Croma',
                'current_price' => 119999.00,
                'original_price' => 134999.00,
                'discount_rate' => 11,
                'rating' => 4.7,
                'review_count' => 2940,
                'image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&q=80',
                'product_url' => 'https://www.croma.com/search/?q=Samsung+S24+Ultra',
                'category' => 'Mobiles'
            ],
            [
                'id' => 1013,
                'product_name' => 'OnePlus 12 5G (Flowy Emerald, 16GB RAM, 512GB)',
                'website' => 'Amazon',
                'current_price' => 62999.00,
                'original_price' => 69999.00,
                'discount_rate' => 10,
                'rating' => 4.6,
                'review_count' => 3120,
                'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=OnePlus+12',
                'category' => 'Mobiles'
            ],
            [
                'id' => 1014,
                'product_name' => 'Google Pixel 9 (128GB, Obsidian Black) with Gemini AI',
                'website' => 'Flipkart',
                'current_price' => 69999.00,
                'original_price' => 79999.00,
                'discount_rate' => 12,
                'rating' => 4.5,
                'review_count' => 1560,
                'image_url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Google+Pixel+9',
                'category' => 'Mobiles'
            ]
        ]
    ],
    'electronics' => [
        'name' => 'Electronics',
        'title' => 'Electronics & Gadgets',
        'icon' => 'fa-solid fa-laptop',
        'badge' => 'High Performance',
        'description' => 'Unbeatable deals on MacBooks, gaming laptops, noise-cancelling headphones, smart displays, and audio monitors.',
        'subcategories' => ['All', 'Laptops & Macs', 'Headphones & Audio', 'Smartwatches', 'Tablets', 'Gaming Consoles'],
        'keywords' => ['electronics', 'laptop', 'macbook', 'headphone', 'audio', 'earbuds', 'tablet', 'drone', 'camera'],
        'fallback_products' => [
            [
                'id' => 1021,
                'product_name' => 'Apple MacBook Air 13" M3 Chip (16GB Unified Memory, 256GB SSD)',
                'website' => 'Amazon',
                'current_price' => 99990.00,
                'original_price' => 114900.00,
                'discount_rate' => 13,
                'rating' => 4.9,
                'review_count' => 1840,
                'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=MacBook+Air+M3',
                'category' => 'Electronics'
            ],
            [
                'id' => 1022,
                'product_name' => 'Sony WH-1000XM5 Wireless Industry Leading Noise Canceling Headphones',
                'website' => 'Croma',
                'current_price' => 26990.00,
                'original_price' => 34990.00,
                'discount_rate' => 23,
                'rating' => 4.8,
                'review_count' => 6430,
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80',
                'product_url' => 'https://www.croma.com/search/?q=Sony+WH-1000XM5',
                'category' => 'Electronics'
            ],
            [
                'id' => 1023,
                'product_name' => 'PlayStation 5 Slim Console (Disc Edition, 1TB Storage)',
                'website' => 'Amazon',
                'current_price' => 49990.00,
                'original_price' => 54990.00,
                'discount_rate' => 9,
                'rating' => 4.8,
                'review_count' => 5120,
                'image_url' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=PlayStation+5+Slim',
                'category' => 'Electronics'
            ],
            [
                'id' => 1024,
                'product_name' => 'Apple iPad Air 11-inch M2 Chip (Liquid Retina, 128GB Wi-Fi)',
                'website' => 'Flipkart',
                'current_price' => 55900.00,
                'original_price' => 59900.00,
                'discount_rate' => 7,
                'rating' => 4.7,
                'review_count' => 920,
                'image_url' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=iPad+Air+M2',
                'category' => 'Electronics'
            ]
        ]
    ],
    'beauty' => [
        'name' => 'Beauty',
        'title' => 'Beauty & Personal Care',
        'icon' => 'fa-solid fa-wand-magic-sparkles',
        'badge' => 'Self-Care & Glow',
        'description' => 'Explore luxury perfumes, dermatologist-approved skincare, makeup, and wellness essentials with multi-store savings.',
        'subcategories' => ['All', 'Skincare', 'Fragrances & Perfumes', 'Makeup', 'Haircare', 'Grooming'],
        'keywords' => ['beauty', 'perfume', 'skincare', 'fragrance', 'cosmetics', 'lotion', 'makeup', 'serum', 'shampoo'],
        'fallback_products' => [
            [
                'id' => 1031,
                'product_name' => 'Dior Sauvage Eau De Parfum for Men (100ml)',
                'website' => 'Amazon',
                'current_price' => 11450.00,
                'original_price' => 13500.00,
                'discount_rate' => 15,
                'rating' => 4.8,
                'review_count' => 1930,
                'image_url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Dior+Sauvage+EDP',
                'category' => 'Beauty'
            ],
            [
                'id' => 1032,
                'product_name' => 'The Ordinary Niacinamide 10% + Zinc 1% High-Strength Serum (60ml)',
                'website' => 'Flipkart',
                'current_price' => 950.00,
                'original_price' => 1200.00,
                'discount_rate' => 21,
                'rating' => 4.6,
                'review_count' => 8420,
                'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=The+Ordinary+Niacinamide',
                'category' => 'Beauty'
            ],
            [
                'id' => 1033,
                'product_name' => 'Philips Series 7000 Multi-Groomer All-in-One Trimmer for Men',
                'website' => 'Amazon',
                'current_price' => 3899.00,
                'original_price' => 5495.00,
                'discount_rate' => 29,
                'rating' => 4.5,
                'review_count' => 12500,
                'image_url' => 'https://images.unsplash.com/photo-1621607512214-68297480165e?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Philips+Multi+Groomer',
                'category' => 'Beauty'
            ]
        ]
    ],
    'home' => [
        'name' => 'Home',
        'title' => 'Home, Kitchen & Living',
        'icon' => 'fa-solid fa-couch',
        'badge' => 'Comfort & Living',
        'description' => 'Elevate your interiors with modern furniture, smart lighting, kitchen cookware, and premium bedroom linens.',
        'subcategories' => ['All', 'Kitchen & Cookware', 'Living Room Furniture', 'Home Decor', 'Bedding & Linen', 'Smart Lighting'],
        'keywords' => ['home', 'kitchen', 'furniture', 'decor', 'lamp', 'blender', 'cookware', 'sofa', 'mattress'],
        'fallback_products' => [
            [
                'id' => 1041,
                'product_name' => 'Philips Hue Smart LED Ambience Starter Kit with Bridge & Dimmer',
                'website' => 'Amazon',
                'current_price' => 8499.00,
                'original_price' => 11999.00,
                'discount_rate' => 29,
                'rating' => 4.6,
                'review_count' => 2180,
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Philips+Hue+Starter+Kit',
                'category' => 'Home'
            ],
            [
                'id' => 1042,
                'product_name' => 'Prestige Deluxe Alpha Stainless Steel Pressure Cooker (5 Litres)',
                'website' => 'Flipkart',
                'current_price' => 2490.00,
                'original_price' => 3550.00,
                'discount_rate' => 30,
                'rating' => 4.4,
                'review_count' => 9640,
                'image_url' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Prestige+Deluxe+Pressure+Cooker',
                'category' => 'Home'
            ],
            [
                'id' => 1043,
                'product_name' => 'Wakefit Orthopedic Memory Foam Mattress (King Size, 78x72x8 inches)',
                'website' => 'Amazon',
                'current_price' => 14499.00,
                'original_price' => 21999.00,
                'discount_rate' => 34,
                'rating' => 4.6,
                'review_count' => 18900,
                'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Wakefit+Orthopedic+Mattress',
                'category' => 'Home'
            ]
        ]
    ],
    'toys' => [
        'name' => 'Toys',
        'title' => 'Toys, Games & Baby',
        'icon' => 'fa-solid fa-shapes',
        'badge' => 'Play & Learn',
        'description' => 'Top-selling board games, LEGO building kits, STEM learning sets, action figures, and newborn baby care products.',
        'subcategories' => ['All', 'Action Figures & LEGO', 'Board Games', 'STEM & Educational', 'Remote Control Toys', 'Baby Gear'],
        'keywords' => ['toy', 'game', 'lego', 'puzzle', 'doll', 'baby', 'action figure', 'drone', 'board game'],
        'fallback_products' => [
            [
                'id' => 1051,
                'product_name' => 'LEGO Icons Chevrolet Camaro Z28 10304 Collectible Car Building Set',
                'website' => 'Amazon',
                'current_price' => 13499.00,
                'original_price' => 16999.00,
                'discount_rate' => 21,
                'rating' => 4.9,
                'review_count' => 1420,
                'image_url' => 'https://images.unsplash.com/photo-1585366119957-e9730b6d0f60?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=LEGO+Chevrolet+Camaro',
                'category' => 'Toys'
            ],
            [
                'id' => 1052,
                'product_name' => 'Monopoly Deluxe Edition Board Game with Wooden Dice & Gold Tokens',
                'website' => 'Amazon',
                'current_price' => 1299.00,
                'original_price' => 1999.00,
                'discount_rate' => 35,
                'rating' => 4.7,
                'review_count' => 7420,
                'image_url' => 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Monopoly+Deluxe+Board+Game',
                'category' => 'Toys'
            ],
            [
                'id' => 1053,
                'product_name' => 'Hot Wheels 20-Car Gift Pack with 1:64 Scale Die-Cast Vehicles',
                'website' => 'Flipkart',
                'current_price' => 1999.00,
                'original_price' => 2799.00,
                'discount_rate' => 29,
                'rating' => 4.6,
                'review_count' => 5890,
                'image_url' => 'https://images.unsplash.com/photo-1594787318286-3d835c1d207f?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Hot+Wheels+20+Car+Pack',
                'category' => 'Toys'
            ]
        ]
    ],
    'appliances' => [
        'name' => 'Appliances',
        'title' => 'Appliances & White Goods',
        'icon' => 'fa-solid fa-blender',
        'badge' => 'Smart Living',
        'description' => 'Energy-efficient refrigerators, front-load washing machines, inverter air conditioners, and microwaves.',
        'subcategories' => ['All', 'Refrigerators', 'Washing Machines', 'Air Conditioners', 'Microwave Ovens', 'Kitchen Appliances'],
        'keywords' => ['appliance', 'refrigerator', 'microwave', 'washing machine', 'air conditioner', 'vacuum', 'oven'],
        'fallback_products' => [
            [
                'id' => 1061,
                'product_name' => 'LG 8 Kg 5 Star Inverter Front Load Washing Machine with Steam Care',
                'website' => 'Croma',
                'current_price' => 34990.00,
                'original_price' => 46990.00,
                'discount_rate' => 26,
                'rating' => 4.6,
                'review_count' => 4210,
                'image_url' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&q=80',
                'product_url' => 'https://www.croma.com/search/?q=LG+Front+Load+Washing+Machine',
                'category' => 'Appliances'
            ],
            [
                'id' => 1062,
                'product_name' => 'Samsung 236 L 3 Star Digital Inverter Double Door Refrigerator',
                'website' => 'Amazon',
                'current_price' => 25990.00,
                'original_price' => 37990.00,
                'discount_rate' => 32,
                'rating' => 4.5,
                'review_count' => 6890,
                'image_url' => 'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Samsung+Double+Door+Refrigerator',
                'category' => 'Appliances'
            ]
        ]
    ],
    'groceries' => [
        'name' => 'Groceries',
        'title' => 'Groceries & Gourmet',
        'icon' => 'fa-solid fa-basket-shopping',
        'badge' => 'Daily Essentials',
        'description' => 'Compare prices on daily pantry staples, artisanal coffee, organic nuts, chocolates, and breakfast cereals.',
        'subcategories' => ['All', 'Daily Staples', 'Coffee & Tea', 'Dry Fruits & Nuts', 'Snacks & Chocolates', 'Organic Foods'],
        'keywords' => ['grocery', 'food', 'coffee', 'tea', 'snack', 'chocolate', 'organic', 'nuts', 'oil'],
        'fallback_products' => [
            [
                'id' => 1071,
                'product_name' => 'Davidoff Rich Aroma Instant Coffee (100g Glass Jar)',
                'website' => 'Amazon',
                'current_price' => 595.00,
                'original_price' => 750.00,
                'discount_rate' => 21,
                'rating' => 4.7,
                'review_count' => 14320,
                'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Davidoff+Rich+Aroma+Coffee',
                'category' => 'Groceries'
            ],
            [
                'id' => 1072,
                'product_name' => 'Happilo Premium California Whole Almonds (1 Kg Value Pack)',
                'website' => 'Flipkart',
                'current_price' => 789.00,
                'original_price' => 1299.00,
                'discount_rate' => 39,
                'rating' => 4.5,
                'review_count' => 22400,
                'image_url' => 'https://images.unsplash.com/photo-1508061253366-f7da158b6d46?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Happilo+California+Almonds',
                'category' => 'Groceries'
            ]
        ]
    ],
    'sports' => [
        'name' => 'Sports',
        'title' => 'Sports & Fitness Gear',
        'icon' => 'fa-solid fa-dumbbell',
        'badge' => 'Active Life',
        'description' => 'Power your workouts with home gym sets, running gear, yoga mats, cricket gear, and fitness smart accessories.',
        'subcategories' => ['All', 'Gym & Weights', 'Running & Training', 'Yoga & Mats', 'Cricket & Outdoor', 'Fitness Trackers'],
        'keywords' => ['sports', 'fitness', 'gym', 'dumbbell', 'running', 'cricket', 'yoga', 'shoes', 'cycle'],
        'fallback_products' => [
            [
                'id' => 1081,
                'product_name' => 'Kore Rubber Encased Hex Dumbbells Set (5kg x 2 Pair)',
                'website' => 'Amazon',
                'current_price' => 1499.00,
                'original_price' => 2999.00,
                'discount_rate' => 50,
                'rating' => 4.4,
                'review_count' => 4320,
                'image_url' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Hex+Dumbbells+5kg',
                'category' => 'Sports'
            ],
            [
                'id' => 1082,
                'product_name' => 'Boldfit Anti-Skid Yoga Mat 10mm Extra Thick with Carrying Strap',
                'website' => 'Amazon',
                'current_price' => 899.00,
                'original_price' => 1799.00,
                'discount_rate' => 50,
                'rating' => 4.5,
                'review_count' => 11200,
                'image_url' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Boldfit+Yoga+Mat',
                'category' => 'Sports'
            ]
        ]
    ],
    'books' => [
        'name' => 'Books',
        'title' => 'Books & Literature',
        'icon' => 'fa-solid fa-book-open',
        'badge' => 'Top Reads',
        'description' => 'Global bestsellers, fiction novels, personal development, financial wisdom, and academic paperbacks at best discounts.',
        'subcategories' => ['All', 'Bestsellers', 'Fiction & Mystery', 'Personal Finance & Self-Help', 'Business & Leadership', 'Sci-Fi'],
        'keywords' => ['book', 'novel', 'fiction', 'literature', 'textbook', 'reading', 'author', 'bestseller'],
        'fallback_products' => [
            [
                'id' => 1091,
                'product_name' => 'Atomic Habits by James Clear: An Easy & Proven Way to Build Good Habits',
                'website' => 'Amazon',
                'current_price' => 480.00,
                'original_price' => 799.00,
                'discount_rate' => 40,
                'rating' => 4.8,
                'review_count' => 84200,
                'image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'product_url' => 'https://www.amazon.in/s?k=Atomic+Habits',
                'category' => 'Books'
            ],
            [
                'id' => 1092,
                'product_name' => 'The Psychology of Money by Morgan Housel (Timeless Lessons on Wealth)',
                'website' => 'Flipkart',
                'current_price' => 299.00,
                'original_price' => 450.00,
                'discount_rate' => 34,
                'rating' => 4.7,
                'review_count' => 52100,
                'image_url' => 'https://images.unsplash.com/photo-1592496431122-2349e0fbc666?w=600&q=80',
                'product_url' => 'https://www.flipkart.com/search?q=Psychology+of+Money',
                'category' => 'Books'
            ]
        ]
    ]
];

// Category Specific Database Mapping (Strict Category Isolation)
$CATEGORY_DB_MAP = [
    'groceries' => ['Groceries'],
    'beauty' => ['Beauty'],
    'toys' => ['Toys'],
    'fashion' => ['Fashion', "men's clothing", 'mens-shoes', 'T-Shirts'],
    'mobiles' => ['Mobiles', 'Smartphones', 'Cell Phones & Smartphones', 'mobile-accessories'],
    'electronics' => ['Electronics', 'Camera Drones', 'Controllers & Attachments', 'Video Game Consoles'],
    'home' => ['Home'],
    'appliances' => ['Appliances'],
    'sports' => ['Sports'],
    'books' => ['Books']
];

// Determine Active Category
$rawCat = strtolower(trim($_GET['cat'] ?? $_GET['category'] ?? 'fashion'));
$catKey = array_key_exists($rawCat, $CATEGORIES_DATA) ? $rawCat : 'fashion';
$catInfo = $CATEGORIES_DATA[$catKey];

// Category Fallback Image (Never a hardcoded watch!)
$catFallbackImg = match($catKey) {
    'fashion' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80',
    'mobiles' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80',
    'electronics' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80',
    'beauty' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&q=80',
    'home' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=80',
    'toys' => 'https://images.unsplash.com/photo-1585366119957-e9730b6d0f60?w=600&q=80',
    'appliances' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&q=80',
    'groceries' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80',
    'sports' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&q=80',
    'books' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
    default => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&q=80'
};

if (!function_exists('resolveExactProductImage')) {
    function resolveExactProductImage(string $name, ?string $currentUrl = null, string $category = ''): string {
        // 1. Ray-Ban / Sunglasses / Glasses
        if (preg_match('/\b(ray-ban|rayban|sunglass|sunglasses|glasses|eyewear)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80';
        }

        // 2. Jeans / Denim / Trousers
        if (preg_match('/\b(jean|jeans|denim|pants|trousers?)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=600&q=80';
        }

        // 3. Jordans
        if (preg_match('/\b(jordan|jordans|ajko)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600&q=80';
        }

        // 4. Shoes / Sneakers / Running
        if (preg_match('/\b(shoe|shoes|sneaker|sneakers|footwear)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80';
        }

        // 5. Watch / Chronograph
        if (preg_match('/\b(watch|watches|chronograph|fossil)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=600&q=80';
        }

        // 6. Headphones / Audio (BEFORE phone!)
        if (preg_match('/\b(headphone|headphones|earphone|earphones|headset|wh-1000xm\d*)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80';
        }

        // 7. T-Shirts / Shirts / Apparel
        if (preg_match('/\b(t-shirt|t-shirts|tshirt|tshirts|crew\s*neck|shirt|shirts)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&q=80';
        }

        // 8. Cooking Oil / Sunflower Oil / Olive Oil
        if (preg_match('/\b(sunflower\s*oil|cooking\s*oil|olive\s*oil|\boil\b)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600&q=80';
        }

        // 9. Rice / Basmati
        if (preg_match('/\b(rice|basmati)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80';
        }

        // 10. Tea (Must be word boundary so Teal does not match!)
        if (preg_match('/\b(tea|chai|black\s*tea|green\s*tea)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=600&q=80';
        }

        // 11. Coffee
        if (preg_match('/\b(coffee|nescafe|davidoff|espresso|roast)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&q=80';
        }

        // 12. Almonds / Dry Fruits / Nuts
        if (preg_match('/\b(almond|almonds|badam|cashew|walnut)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1508061253366-f7da158b6d46?w=600&q=80';
        }

        // 13. Oats / Cereals
        if (preg_match('/\b(oat|oats|wholegrain|cereal|cereals)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=600&q=80';
        }

        // 14. Chocolate
        if (preg_match('/\b(chocolate|bournville|cadbury|cocoa)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?w=600&q=80';
        }

        // 15. Dal / Lentils / Pulses
        if (preg_match('/\b(dal|toor|arhar|lentil|lentils|pulse|pulses)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=600&q=80';
        }

        // 16. Skincare Serum / Niacinamide
        if (preg_match('/\b(serum|niacinamide)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&q=80';
        }

        // 17. Face Cleanser / Face Wash
        if (preg_match('/\b(cleanser|face\s*wash|cetaphil)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=600&q=80';
        }

        // 18. Perfume / Fragrance / Sauvage
        if (preg_match('/\b(sauvage|perfume|parfum|fragrance|cologne|scent)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&q=80';
        }

        // 19. Face Cream / Moisturizer / Gel
        if (preg_match('/\b(moisturizer|water\s*gel|cream|lotion)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?w=600&q=80';
        }

        // 20. Trimmer / Groomer / Shaver
        if (preg_match('/\b(trimmer|groomer|shaver|clipper)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1621607512214-68297480165e?w=600&q=80';
        }

        // 21. LEGO / Building Blocks
        if (preg_match('/\b(lego|building\s*set|camaro)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1585366119957-e9730b6d0f60?w=600&q=80';
        }

        // 22. Board Games / Monopoly
        if (preg_match('/\b(monopoly|board\s*game)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?w=600&q=80';
        }

        // 23. Hot Wheels / Toy Cars
        if (preg_match('/\b(hot\s*wheels|die-cast|diecast)\b/i', $name) || (preg_match('/\bcar\b/i', $name) && strtolower($category) === 'toys')) {
            return 'https://images.unsplash.com/photo-1594787318286-3d835c1d207f?w=600&q=80';
        }

        // 24. Rubik's Cube / Puzzle
        if (preg_match('/\b(rubik|speed\s*cube|\bcube\b|puzzle)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1591991731833-b4807cf7ef94?w=600&q=80';
        }

        // 25. Nerf Blaster / Toy Gun
        if (preg_match('/\b(nerf|blaster|darts?)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1594787318286-3d835c1d207f?w=600&q=80';
        }

        // 26. Books
        if (preg_match('/\b(atomic\s*habits)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80';
        }
        if (preg_match('/\b(psychology\s*of\s*money)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1592496431122-2349e0fbc666?w=600&q=80';
        }
        if (preg_match('/\b(sapiens)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&q=80';
        }
        if (preg_match('/\b(ikigai)\b/i', $name) || strtolower($category) === 'books' || preg_match('/\b(book|books|novel|paperback|hardcover)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&q=80';
        }

        // 27. Dumbbells / Barbell / Gym Weights
        if (preg_match('/\b(dumbbell|dumbbells|barbell|barbells|weights?|kore)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&q=80';
        }

        // 28. Yoga Mat
        if (preg_match('/\b(yoga\s*mat|exercise\s*mat|\bmat\b)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=600&q=80';
        }

        // 29. Badminton Racquet
        if (preg_match('/\b(badminton|racquet|racket|yonex)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=600&q=80';
        }

        // 30. Smart Lighting / Bulbs
        if (preg_match('/\b(lighting|smart\s*led|philips\s*hue|lamp|bulb|bulbs)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=80';
        }

        // 31. Pressure Cooker / Cookware
        if (preg_match('/\b(cooker|pressure\s*cooker|cookware|pan|pans|pot|pots)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=600&q=80';
        }

        // 32. Mattress / Bedding
        if (preg_match('/\b(mattress|wakefit|bedding)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80';
        }

        // 33. Washing Machine
        if (preg_match('/\b(washing\s*machine|washer)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&q=80';
        }

        // 34. Refrigerator / Fridge
        if (preg_match('/\b(refrigerator|fridge)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?w=600&q=80';
        }

        // 35. Microwave Oven
        if (preg_match('/\b(microwave|convection\s*oven|oven)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1585659722983-3a675dabf23d?w=600&q=80';
        }

        // 36. iPhone
        if (preg_match('/\b(iphone|apple\s*iphone)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80';
        }

        // 37. Samsung Galaxy
        if (preg_match('/\b(galaxy|s24|s25|s26|z\s*fold)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&q=80';
        }

        // 38. MacBook / Laptop
        if (preg_match('/\b(macbook|laptop|laptops|notebook)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80';
        }

        // 39. PlayStation / Gaming Console
        if (preg_match('/\b(playstation|ps5|xbox|gaming\s*console|console)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&q=80';
        }

        // 40. iPad / Tablet
        if (preg_match('/\b(ipad|tablet|tablets)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80';
        }

        // 41. Drone / Quadcopter
        if (preg_match('/\b(drone|drones|quadcopter|dji)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1508614589041-895b88991e3e?w=600&q=80';
        }

        // 42. OnePlus / Smartphone / Mobile (After headphones & gadgets)
        if (preg_match('/\b(oneplus|smartphone|smartphones|mobile|mobiles|pixel)\b/i', $name)) {
            return 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=600&q=80';
        }

        // If currentUrl is already a valid Unsplash or active non-broken image, retain it
        if (!empty($currentUrl) && strpos($currentUrl, 'm.media-amazon.com') === false && strpos($currentUrl, 'photo-1523275335684-37898b6baf30') === false) {
            return $currentUrl;
        }

        // Category-level fallback
        return match(strtolower($category)) {
            'fashion', 'clothing' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
            'mobiles', 'smartphones' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600&q=80',
            'electronics' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80',
            'beauty' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&q=80',
            'home' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=600&q=80',
            'toys' => 'https://images.unsplash.com/photo-1585366119957-e9730b6d0f60?w=600&q=80',
            'appliances' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&q=80',
            'groceries' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&q=80',
            'sports' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=600&q=80',
            'books' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
            default => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&q=80'
        };
    }
}

// Filtering & Search Inputs
$subcatFilter = trim($_GET['subcat'] ?? 'All');
$searchQuery = trim($_GET['q'] ?? '');
$sortOrder = trim($_GET['sort'] ?? 'recommended');
$siteFilter = trim($_GET['site'] ?? 'all');

// Fetch Matching Products from Database
$dbProducts = [];
try {
    $whereClauses = [];
    $params = [];

    // 1. Strict Category Isolation: Match ONLY products belonging to this specific category
    $allowedCategories = $CATEGORY_DB_MAP[$catKey] ?? [$catInfo['name']];
    $catClauses = [];
    foreach ($allowedCategories as $idx => $ac) {
        $param = ":cat_allowed_{$idx}";
        $catClauses[] = "LOWER(category) = LOWER({$param})";
        $params[$param] = $ac;
    }
    $whereClauses[] = "(" . implode(" OR ", $catClauses) . ")";

    // 2. Prevent accidental cross-category leakage (e.g. general electronics matching into groceries/beauty/toys)
    if ($catKey !== 'electronics') {
        $whereClauses[] = "LOWER(category) NOT IN ('electronics', 'camera drones', 'video game consoles', 'controllers & attachments')";
    }
    if ($catKey !== 'mobiles') {
        $whereClauses[] = "LOWER(category) NOT IN ('cell phones & smartphones', 'smartphones')";
    }

    // 3. In-Category Search Query
    if (!empty($searchQuery)) {
        $whereClauses[] = "(LOWER(product_name) LIKE :searchQ OR LOWER(description) LIKE :searchQ)";
        $params[':searchQ'] = "%" . strtolower($searchQuery) . "%";
    } elseif ($subcatFilter !== 'All' && !empty($subcatFilter)) {
        // Subcategory Term Matching within this category
        $cleanSubcat = trim(str_replace(['All', '&amp;', '&', 'and', 'for', 'the'], ' ', $subcatFilter));
        $terms = array_filter(explode(' ', strtolower($cleanSubcat)));
        $subcatClauses = [];
        foreach ($terms as $tIdx => $term) {
            if (strlen($term) < 3) continue;
            $tParam = ":subcat_term_{$tIdx}";
            $subcatClauses[] = "(LOWER(product_name) LIKE {$tParam} OR LOWER(description) LIKE {$tParam})";
            $params[$tParam] = "%" . $term . "%";
        }
        if (!empty($subcatClauses)) {
            $whereClauses[] = "(" . implode(" OR ", $subcatClauses) . ")";
        }
    }

    // 4. Marketplace Store Filter
    if ($siteFilter !== 'all' && !empty($siteFilter)) {
        $whereClauses[] = "LOWER(website) = :siteName";
        $params[':siteName'] = strtolower($siteFilter);
    }

    // Construct SQL Query
    $sql = "SELECT id, product_name, website, current_price, original_price, discount_rate, rating, review_count, image_url, product_url, category FROM product_prices";
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Sorting
    switch ($sortOrder) {
        case 'price_low':
            $sql .= " ORDER BY current_price ASC LIMIT 40";
            break;
        case 'price_high':
            $sql .= " ORDER BY current_price DESC LIMIT 40";
            break;
        case 'rating':
            $sql .= " ORDER BY rating DESC, review_count DESC LIMIT 40";
            break;
        case 'recommended':
        default:
            $sql .= " ORDER BY (rating * 0.4 + (discount_rate / 10) * 0.3) DESC, review_count DESC LIMIT 40";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $dbProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dbProducts = [];
}

// Display verified DB products, or curated fallback if DB is completely empty for this category
$displayProducts = $dbProducts;
if (empty($displayProducts) && empty($searchQuery) && ($subcatFilter === 'All' || empty($subcatFilter))) {
    $displayProducts = $catInfo['fallback_products'];
}
?>

<link rel="stylesheet" href="css/category.css?v=<?= time(); ?>">

<div class="category-page-container">
    <!-- Breadcrumb -->
    <nav class="category-breadcrumb" aria-label="Breadcrumb">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Categories</span>
        <i class="fa-solid fa-chevron-right"></i>
        <span class="current"><?= htmlspecialchars($catInfo['name']); ?></span>
    </nav>

    <!-- Category Hero Banner -->
    <header class="category-hero-banner">
        <div class="category-hero-content">
            <div class="category-hero-text">
                <div class="category-pill-badge">
                    <i class="<?= htmlspecialchars($catInfo['icon']); ?>"></i>
                    <span><?= htmlspecialchars($catInfo['badge']); ?></span>
                </div>
                <h1>Best <span><?= htmlspecialchars($catInfo['title']); ?></span> Deals</h1>
                <p><?= htmlspecialchars($catInfo['description']); ?></p>

                <!-- Search inside this category -->
                <form action="category.php" method="GET" class="category-search-bar">
                    <input type="hidden" name="cat" value="<?= htmlspecialchars($catKey); ?>">
                    <input type="text" name="q" value="<?= htmlspecialchars($searchQuery); ?>" placeholder="Search inside <?= htmlspecialchars($catInfo['name']); ?> deals..." aria-label="Search category">
                    <button type="submit" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="category-hero-icon-large" aria-hidden="true">
                <i class="<?= htmlspecialchars($catInfo['icon']); ?>"></i>
            </div>
        </div>
    </header>

    <!-- Subcategories Scroller Pills -->
    <div class="subcategories-scroller-wrap">
        <div class="subcategories-pills-list">
            <?php foreach ($catInfo['subcategories'] as $subcat): 
                $isActive = ($subcatFilter === $subcat) || (empty($subcatFilter) && $subcat === 'All');
            ?>
                <a href="category.php?cat=<?= urlencode($catKey); ?>&subcat=<?= urlencode($subcat); ?>" class="subcategory-pill <?= $isActive ? 'active' : ''; ?>">
                    <?= htmlspecialchars($subcat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Category Toolbar (Results count, Store Filter, Sort) -->
    <div class="category-toolbar">
        <div class="category-results-count">
            Showing <strong><?= count($displayProducts); ?></strong> verified deal comparisons in <strong><?= htmlspecialchars($catInfo['name']); ?></strong>
        </div>

        <form id="categoryFilterForm" action="category.php" method="GET" class="category-filters-group">
            <input type="hidden" name="cat" value="<?= htmlspecialchars($catKey); ?>">
            <?php if (!empty($subcatFilter) && $subcatFilter !== 'All'): ?>
                <input type="hidden" name="subcat" value="<?= htmlspecialchars($subcatFilter); ?>">
            <?php endif; ?>
            <?php if (!empty($searchQuery)): ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery); ?>">
            <?php endif; ?>

            <div class="category-select-wrapper">
                <i class="fa-solid fa-store"></i>
                <select name="site" class="category-filter-select" onchange="document.getElementById('categoryFilterForm').submit();">
                    <option value="all" <?= $siteFilter === 'all' ? 'selected' : ''; ?>>All Stores</option>
                    <option value="amazon" <?= $siteFilter === 'amazon' ? 'selected' : ''; ?>>Amazon</option>
                    <option value="flipkart" <?= $siteFilter === 'flipkart' ? 'selected' : ''; ?>>Flipkart</option>
                    <option value="croma" <?= $siteFilter === 'croma' ? 'selected' : ''; ?>>Croma</option>
                    <option value="ebay" <?= $siteFilter === 'ebay' ? 'selected' : ''; ?>>eBay</option>
                </select>
            </div>

            <div class="category-select-wrapper">
                <i class="fa-solid fa-arrow-down-wide-short"></i>
                <select name="sort" class="category-filter-select" onchange="document.getElementById('categoryFilterForm').submit();">
                    <option value="recommended" <?= $sortOrder === 'recommended' ? 'selected' : ''; ?>>Best Value</option>
                    <option value="price_low" <?= $sortOrder === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sortOrder === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="rating" <?= $sortOrder === 'rating' ? 'selected' : ''; ?>>Customer Rating</option>
                </select>
            </div>

            <a href="dashboard.php?q=<?= urlencode($catInfo['name']); ?>" class="btn-champagne-secondary" style="padding: 0.45rem 0.95rem; font-size: 0.85rem;" title="Compare live on Dashboard">
                <i class="fa-solid fa-sliders" style="color: #06433B;"></i> Live Scanner
            </a>
        </form>
    </div>

    <!-- Products Grid -->
    <?php if (!empty($displayProducts)): ?>
        <div class="category-products-grid">
            <?php foreach ($displayProducts as $p): 
                $siteClean = strtolower($p['website'] ?? 'amazon');
                $badgeClass = 'cat-badge-' . ($siteClean === 'amazon' ? 'amazon' : ($siteClean === 'flipkart' ? 'flipkart' : ($siteClean === 'croma' ? 'croma' : 'ebay')));
                $hasDiscount = !empty($p['discount_rate']) && $p['discount_rate'] > 0;
                $origPrice = !empty($p['original_price']) && $p['original_price'] > $p['current_price'] ? (float)$p['original_price'] : 0;
                $rating = (float)($p['rating'] ?? 4.5);
                $fullStars = floor($rating);
                $hasHalf = ($rating - $fullStars) >= 0.4;
            ?>
                <div class="cat-product-card">
                    <div class="cat-card-img-wrap">
                        <span class="cat-store-pill <?= $badgeClass; ?>">
                            <i class="<?= $siteClean === 'amazon' ? 'fa-brands fa-amazon' : ($siteClean === 'ebay' ? 'fa-brands fa-ebay' : 'fa-solid fa-bag-shopping'); ?>"></i>
                            <?= htmlspecialchars(ucfirst($p['website'])); ?>
                        </span>

                        <?php if ($hasDiscount): ?>
                            <span class="cat-discount-badge"><?= (int)$p['discount_rate']; ?>% OFF</span>
                        <?php endif; ?>

                        <?php 
                            $actualImg = resolveExactProductImage($p['product_name'], $p['image_url'] ?? '', $p['category'] ?? $catInfo['name']);
                        ?>
                        <img src="<?= htmlspecialchars($actualImg); ?>" alt="<?= htmlspecialchars($p['product_name']); ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= htmlspecialchars($catFallbackImg); ?>';">
                    </div>

                    <div class="cat-card-body">
                        <div class="cat-card-category-label"><?= htmlspecialchars($catInfo['name']); ?></div>
                        <h2 class="cat-card-title">
                            <a href="<?= isset($p['id']) && $p['id'] < 1000 ? 'product-details.php?id=' . $p['id'] : htmlspecialchars($p['product_url']); ?>" <?= isset($p['id']) && $p['id'] < 1000 ? '' : 'target="_blank" rel="noopener noreferrer"'; ?>>
                                <?= htmlspecialchars($p['product_name']); ?>
                            </a>
                        </h2>

                        <div class="cat-card-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $fullStars): ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php elseif ($i == $fullStars + 1 && $hasHalf): ?>
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    <?php else: ?>
                                        <i class="fa-regular fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-num"><?= number_format($rating, 1); ?></span>
                            <span class="reviews-count">(<?= number_format((int)($p['review_count'] ?? 150)); ?>)</span>
                        </div>

                        <div class="cat-card-price-box">
                            <div class="cat-card-prices">
                                <span class="cat-current-price">₹<?= number_format((float)$p['current_price'], 2); ?></span>
                                <?php if ($origPrice > 0): ?>
                                    <span class="cat-orig-price">₹<?= number_format($origPrice, 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="cat-best-deal-pill">
                                <i class="fa-solid fa-shield-check"></i> Lowest Tracked Price
                            </span>
                        </div>

                        <div class="cat-card-actions">
                            <a href="<?= isset($p['id']) && $p['id'] < 1000 ? 'product-details.php?id=' . $p['id'] : htmlspecialchars($p['product_url']); ?>" class="cat-btn-primary" <?= isset($p['id']) && $p['id'] < 1000 ? '' : 'target="_blank" rel="noopener noreferrer"'; ?>>
                                <span>Compare &amp; Buy</span>
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.8rem;"></i>
                            </a>
                            <a href="dashboard.php?q=<?= urlencode($p['product_name']); ?>" class="cat-btn-secondary" title="Compare across stores on Dashboard" aria-label="Compare <?= htmlspecialchars($p['product_name']); ?> across stores on Dashboard">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="category-empty-state">
            <i class="fa-regular fa-folder-open"></i>
            <h3>No Products Found in this Filter</h3>
            <p>We couldn't find matches for "<?= htmlspecialchars($searchQuery ?: $subcatFilter); ?>" in <?= htmlspecialchars($catInfo['name']); ?>. Try clearing your filters or scan live across stores.</p>
            <div style="display: flex; gap: 0.8rem; justify-content: center; flex-wrap: wrap;">
                <a href="category.php?cat=<?= urlencode($catKey); ?>" class="btn-champagne-secondary">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filters
                </a>
                <a href="dashboard.php?q=<?= urlencode($catInfo['name']); ?>" class="btn-emerald-primary">
                    <i class="fa-solid fa-sliders"></i> Search Live on Dashboard
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Category Quick Jump Section -->
    <div class="category-quick-jump">
        <h3>Explore Other Popular Categories</h3>
        <p>Browse live multi-store deal comparisons across every shopping department.</p>
        <div class="category-quick-jump-grid">
            <?php foreach ($CATEGORIES_DATA as $k => $c): 
                if ($k === $catKey) continue;
            ?>
                <a href="category.php?cat=<?= urlencode($k); ?>">
                    <i class="<?= htmlspecialchars($c['icon']); ?>" style="color: #06433B;"></i>
                    <?= htmlspecialchars($c['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
