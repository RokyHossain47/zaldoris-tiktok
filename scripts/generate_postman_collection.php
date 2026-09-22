<?php

// Postman Collection Builder for Zaldoris API v1

$collection = [
    'info' => [
        '_postman_id' => 'zaldoris-api-v1-collection',
        'name' => 'Zaldoris TikTok E-Commerce API v1',
        'description' => 'Complete REST API collection for Zaldoris TikTok Live E-Commerce Platform matching all 24 specification modules.',
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'variable' => [
        [
            'key' => 'baseUrl',
            'value' => 'http://127.0.0.1:8000/api/v1',
            'type' => 'string'
        ],
        [
            'key' => 'token',
            'value' => '',
            'type' => 'string'
        ]
    ],
    'auth' => [
        'type' => 'bearer',
        'bearer' => [
            [
                'key' => 'token',
                'value' => '{{token}}',
                'type' => 'string'
            ]
        ]
    ],
    'item' => []
];

function makeRequest($name, $method, $path, $body = null, $auth = true, $testScript = null, $queryParams = []) {
    $urlParts = array_values(array_filter(explode('/', trim($path, '/'))));
    $query = [];
    foreach ($queryParams as $k => $v) {
        $query[] = ['key' => $k, 'value' => (string)$v];
    }

    $req = [
        'name' => $name,
        'request' => [
            'method' => strtoupper($method),
            'header' => [
                [
                    'key' => 'Accept',
                    'value' => 'application/json',
                    'type' => 'text'
                ],
                [
                    'key' => 'Content-Type',
                    'value' => 'application/json',
                    'type' => 'text'
                ]
            ],
            'url' => [
                'raw' => '{{baseUrl}}/' . ltrim($path, '/'),
                'host' => ['{{baseUrl}}'],
                'path' => $urlParts,
                'query' => $query
            ]
        ]
    ];

    if (!$auth) {
        $req['request']['auth'] = ['type' => 'noauth'];
    }

    if ($body !== null) {
        $req['request']['body'] = [
            'mode' => 'raw',
            'raw' => is_string($body) ? $body : json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ];
    }

    if ($testScript) {
        $req['event'] = [
            [
                'listen' => 'test',
                'script' => [
                    'type' => 'text/javascript',
                    'exec' => explode("\n", $testScript)
                ]
            ]
        ];
    }

    return $req;
}

// 1. App Config
$collection['item'][] = [
    'name' => '01. App Config & Bootstrap',
    'item' => [
        makeRequest('Get App Configuration', 'GET', '/app-config', null, false)
    ]
];

// 2. Authentication
$loginScript = "if (pm.response.code === 200) {\n    var jsonData = pm.response.json();\n    if (jsonData.data && jsonData.data.token) {\n        pm.collectionVariables.set('token', jsonData.data.token);\n    }\n}";
$collection['item'][] = [
    'name' => '02. Authentication & Verification',
    'item' => [
        makeRequest('Register New User', 'POST', '/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'username' => 'janedoe',
            'phone' => '+14155552671',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ], false, $loginScript),
        makeRequest('User Login', 'POST', '/auth/login', [
            'email' => 'admin@zaldoris.com',
            'password' => 'password',
            'device_name' => 'iPhone 15 Pro'
        ], false, $loginScript),
        makeRequest('Social OAuth Callback/Exchange', 'POST', '/auth/social/google', [
            'provider_token' => 'mock_google_oauth_token_xyz',
            'device_name' => 'Mobile Safari'
        ], false),
        makeRequest('Refresh Access Token', 'POST', '/auth/refresh', []),
        makeRequest('User Logout', 'POST', '/auth/logout', []),
        makeRequest('Verify Auth Challenge (OTP)', 'POST', '/auth/challenges/1/verify', [
            'code' => '123456'
        ], false),
        makeRequest('Resend Auth Challenge (OTP)', 'POST', '/auth/challenges/1/resend', [], false),
        makeRequest('Request Password Reset OTP', 'POST', '/auth/password-reset/requests', [
            'contact' => 'janedoe@example.com'
        ], false),
        makeRequest('Complete Password Reset', 'POST', '/auth/password-reset/complete', [
            'token' => 'rst_sample_token_123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ], false)
    ]
];

// 3. User Profile & Preferences
$collection['item'][] = [
    'name' => '03. User Profile & Preferences',
    'item' => [
        makeRequest('Get Current User Profile (/me)', 'GET', '/me'),
        makeRequest('Update Current Profile', 'PUT', '/me', [
            'name' => 'Jane Updated',
            'bio' => 'Fashion and live shopping enthusiast 🛍️✨',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb'
        ]),
        makeRequest('Get User Dashboard Overview', 'GET', '/me/dashboard'),
        makeRequest('Get Public User Profile', 'GET', '/users/1', null, false),
        makeRequest('Get User Preferences', 'GET', '/me/preferences'),
        makeRequest('Update User Preferences', 'PUT', '/me/preferences', [
            'currency' => 'USD',
            'language' => 'en',
            'theme' => 'dark',
            'push_notifications' => true,
            'email_marketing' => false
        ]),
        makeRequest('Request Contact Information Change', 'POST', '/me/contact-change-requests', [
            'type' => 'email',
            'new_value' => 'jane.new@example.com'
        ])
    ]
];

// 4. Addresses & Shipping
$collection['item'][] = [
    'name' => '04. Addresses & Shipping',
    'item' => [
        makeRequest('List User Addresses', 'GET', '/me/addresses'),
        makeRequest('Create New Address', 'POST', '/me/addresses', [
            'recipient_name' => 'Jane Doe',
            'phone' => '+14155552671',
            'street_address' => '742 Evergreen Terrace',
            'apartment_suite' => 'Suite 4B',
            'city' => 'Springfield',
            'state' => 'OR',
            'postal_code' => '97477',
            'country' => 'USA',
            'is_default' => true
        ]),
        makeRequest('Get Single Address', 'GET', '/me/addresses/1'),
        makeRequest('Update Address', 'PUT', '/me/addresses/1', [
            'recipient_name' => 'Jane Doe-Smith',
            'street_address' => '742 Evergreen Terrace Apt 5'
        ]),
        makeRequest('Delete Address', 'DELETE', '/me/addresses/1')
    ]
];

// 5. Social & Follow Graph
$collection['item'][] = [
    'name' => '05. Social & Follow Graph',
    'item' => [
        makeRequest('Get User Followers', 'GET', '/users/1/followers', null, false),
        makeRequest('Get User Following', 'GET', '/users/1/following', null, false),
        makeRequest('Get Follow Suggestions', 'GET', '/me/follow-suggestions'),
        makeRequest('Follow a User', 'POST', '/me/following/2', []),
        makeRequest('Unfollow a User', 'DELETE', '/me/following/2'),
        makeRequest('Get Shared Activity With User', 'GET', '/users/2/shared-activity')
    ]
];

// 6. Home, Discovery & Search
$collection['item'][] = [
    'name' => '06. Home, Discovery & Search',
    'item' => [
        makeRequest('Get Home Feeds (Live, Featured, Auctions)', 'GET', '/home', null, false),
        makeRequest('List Product Categories', 'GET', '/categories', null, false),
        makeRequest('Get Search Discovery & Trending', 'GET', '/search/discovery', null, false),
        makeRequest('Execute Search Query', 'GET', '/search', null, false, null, ['q' => 'shoes', 'type' => 'all']),
        makeRequest('Get User Search History', 'GET', '/me/search-history'),
        makeRequest('Clear User Search History', 'DELETE', '/me/search-history')
    ]
];

// 7. Products & Catalog
$collection['item'][] = [
    'name' => '07. Products & Catalog',
    'item' => [
        makeRequest('List Products (Paginated & Filterable)', 'GET', '/products', null, false, null, ['category_id' => 1, 'per_page' => 10]),
        makeRequest('Get Product Details (With Variants & Specs)', 'GET', '/products/1', null, false)
    ]
];

// 8. Wishlist
$collection['item'][] = [
    'name' => '08. Wishlist',
    'item' => [
        makeRequest('Get Current Wishlist Items', 'GET', '/me/wishlist'),
        makeRequest('Add Product to Wishlist', 'POST', '/me/wishlist', [
            'product_id' => 1
        ]),
        makeRequest('Remove Product from Wishlist', 'DELETE', '/me/wishlist/1')
    ]
];

// 9. Cart & Cart Items
$collection['item'][] = [
    'name' => '09. Cart & Cart Items',
    'item' => [
        makeRequest('Get Shopping Cart Summary', 'GET', '/cart'),
        makeRequest('Add Item to Shopping Cart', 'POST', '/cart/items', [
            'product_id' => 1,
            'quantity' => 2,
            'variant' => [
                'color' => 'Midnight Blue',
                'size' => 'L'
            ]
        ]),
        makeRequest('Update Cart Item Quantity', 'PUT', '/cart/items/1', [
            'quantity' => 3
        ]),
        makeRequest('Remove Item from Cart', 'DELETE', '/cart/items/1'),
        makeRequest('Clear Whole Cart', 'DELETE', '/cart')
    ]
];

// 10. Payment Methods
$collection['item'][] = [
    'name' => '10. Payment Methods & Setup Sessions',
    'item' => [
        makeRequest('Get Saved Payment Methods', 'GET', '/me/payment-methods'),
        makeRequest('Create Setup Session (Stripe/PaymentSheet)', 'POST', '/payment-method-setup-sessions', []),
        makeRequest('Delete Saved Payment Method', 'DELETE', '/me/payment-methods/1')
    ]
];

// 11. Checkout & Quotes
$collection['item'][] = [
    'name' => '11. Checkout & Quotes',
    'item' => [
        makeRequest('Create Checkout Quote', 'POST', '/checkout/quotes', [
            'shipping_address_id' => 1,
            'billing_address_id' => 1,
            'coupon_code' => 'WELCOME10',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                    'variant' => ['color' => 'Black']
                ]
            ]
        ]),
        makeRequest('Get Checkout Quote Details', 'GET', '/checkout/quotes/1'),
        makeRequest('Confirm Checkout Quote (Create Order & Payment)', 'POST', '/checkout/quotes/1/confirm', [
            'payment_method_id' => 1,
            'idempotency_key' => 'quote_confirm_' . time()
        ])
    ]
];

// 12. Payments & 3DS
$collection['item'][] = [
    'name' => '12. Payments & 3DS Challenges',
    'item' => [
        makeRequest('Get Payment Status', 'GET', '/payments/1'),
        makeRequest('Confirm / Capture Payment', 'POST', '/payments/1/confirm', [
            'payment_token' => 'tok_stripe_3ds_confirmed_992'
        ]),
        makeRequest('Verify Payment 3DS Challenge', 'POST', '/payments/1/challenges/challenge_123/verify', [
            'auth_result' => 'success'
        ])
    ]
];

// 13. Orders, Tracking & Returns
$collection['item'][] = [
    'name' => '13. Orders, Tracking & Returns',
    'item' => [
        makeRequest('Get Order History', 'GET', '/me/orders'),
        makeRequest('Get Single Order Details', 'GET', '/orders/1'),
        makeRequest('Re-order Previous Order Items', 'POST', '/orders/1/reorder', []),
        makeRequest('Get Order Shipments & Tracking Timeline', 'GET', '/orders/1/shipments'),
        makeRequest('Check Order Return Eligibility', 'GET', '/orders/1/return-eligibility'),
        makeRequest('Create Order Return Request', 'POST', '/orders/1/returns', [
            'reason' => 'Item defective / wrong size',
            'items' => [
                ['order_item_id' => 1, 'quantity' => 1]
            ]
        ]),
        makeRequest('Get Return Request Details', 'GET', '/returns/1'),
        makeRequest('Get Refund Status', 'GET', '/refunds/1')
    ]
];

// 14. Auctions & Bidding
$collection['item'][] = [
    'name' => '14. Auctions & Bidding',
    'item' => [
        makeRequest('List Live & Upcoming Auctions', 'GET', '/auctions', null, false),
        makeRequest('Get Auction Details', 'GET', '/auctions/1', null, false),
        makeRequest('Get Bid History & Highest Bidders', 'GET', '/auctions/1/bids', null, false),
        makeRequest('Calculate Next Bid Increment Preview', 'GET', '/auctions/1/bid-previews'),
        makeRequest('Place a Live Bid (With Anti-Sniping Protection)', 'POST', '/auctions/1/bids', [
            'bid_amount' => 150.00
        ]),
        makeRequest('Get User Active/Won Auctions', 'GET', '/me/auctions')
    ]
];

// 15. Live Streaming & Interactions
$collection['item'][] = [
    'name' => '15. Live Streaming & Interactions',
    'item' => [
        makeRequest('List Active Live Streams', 'GET', '/live-sessions', null, false),
        makeRequest('Get Live Session Details & Stream URLs', 'GET', '/live-sessions/1', null, false),
        makeRequest('Join Live Session', 'POST', '/live-sessions/1/join', []),
        makeRequest('Send Live Heartbeat Ping', 'POST', '/live-sessions/1/heartbeat', []),
        makeRequest('Leave Live Session', 'POST', '/live-sessions/1/leave', []),
        makeRequest('Get Live Stream Comments', 'GET', '/live-sessions/1/comments', null, false),
        makeRequest('Post a Live Comment', 'POST', '/live-sessions/1/comments', [
            'content' => 'Is this dress available in pink?'
        ]),
        makeRequest('Send Reaction / Like Tap', 'POST', '/live-sessions/1/reactions', [
            'reaction_type' => 'heart',
            'count' => 10
        ]),
        makeRequest('Broadcast Stream Share Event', 'POST', '/live-sessions/1/shares', []),
        makeRequest('Get Showcase / Tagged Products for Stream', 'GET', '/live-sessions/1/items', null, false),
        makeRequest('Pin a Showcase Product (Host Only)', 'POST', '/live-sessions/1/pinned-item', [
            'product_id' => 1
        ])
    ]
];

// 16. PK Battles
$collection['item'][] = [
    'name' => '16. PK Battles',
    'item' => [
        makeRequest('Get PK Battle State & Live Scores', 'GET', '/pk-battles/1', null, false)
    ]
];

// 17. Wallet, Coins & Packages
$collection['item'][] = [
    'name' => '17. Wallet, Coins & Packages',
    'item' => [
        makeRequest('Get User Coin Balance & Wallet', 'GET', '/me/wallet'),
        makeRequest('Get Available Coin Packages', 'GET', '/coin-packages', null, false),
        makeRequest('Create Coin Purchase Quote', 'POST', '/coin-purchase-quotes', [
            'package_id' => 1
        ]),
        makeRequest('Purchase Coins With Stored Balance/Gateway', 'POST', '/coin-purchases', [
            'quote_id' => 1,
            'payment_method_id' => 1
        ]),
        makeRequest('Verify In-App Store Purchase (Apple/Google)', 'POST', '/store-purchases/verify', [
            'store' => 'apple_app_store',
            'receipt_data' => 'MIIT8QYJKoZIhvcNAQcCoIIT4jCCE94CAQExDz...'
        ]),
        makeRequest('Get Wallet Transaction History', 'GET', '/me/wallet/transactions')
    ]
];

// 18. Gifting System
$collection['item'][] = [
    'name' => '18. Gifting System',
    'item' => [
        makeRequest('Get List of Available Virtual Gifts', 'GET', '/gifts', null, false),
        makeRequest('Send Virtual Gift in Stream / PK Battle', 'POST', '/gift-sends', [
            'gift_id' => 1,
            'recipient_id' => 2,
            'live_session_id' => 1,
            'pk_battle_id' => 1,
            'combo_count' => 5
        ]),
        makeRequest('Get User Sent/Received Gifts History', 'GET', '/me/gifts')
    ]
];

// 19. Subscriptions
$collection['item'][] = [
    'name' => '19. Creator Subscriptions',
    'item' => [
        makeRequest('Get Creator Subscription Plans', 'GET', '/creators/1/subscription-plans', null, false),
        makeRequest('Get Subscription Quote', 'POST', '/subscription-quotes', [
            'plan_id' => 1
        ]),
        makeRequest('Subscribe to Creator', 'POST', '/subscriptions', [
            'quote_id' => 1,
            'payment_method_id' => 1
        ]),
        makeRequest('Get User Active Subscriptions', 'GET', '/me/subscriptions')
    ]
];

// 20. Rewards & Tier Conversions
$collection['item'][] = [
    'name' => '20. Rewards & Tier Conversions',
    'item' => [
        makeRequest('Get Reward Tiers & Benefits', 'GET', '/reward-tiers', null, false),
        makeRequest('Get User Loyalty Points & Tier Progress', 'GET', '/me/rewards'),
        makeRequest('Get Points Conversion Quote', 'POST', '/wallet/conversion-quotes', [
            'points' => 500
        ]),
        makeRequest('Convert Loyalty Points to Coins/Coupons', 'POST', '/wallet/conversions', [
            'quote_id' => 1
        ])
    ]
];

// 21. Direct Chat & Messaging
$collection['item'][] = [
    'name' => '21. Direct Chat & Messaging',
    'item' => [
        makeRequest('Get User Conversations List', 'GET', '/me/conversations'),
        makeRequest('Start / Open Conversation with User', 'POST', '/conversations', [
            'recipient_id' => 2
        ]),
        makeRequest('Get Chat Message History', 'GET', '/conversations/1/messages'),
        makeRequest('Send Chat Message', 'POST', '/conversations/1/messages', [
            'message_type' => 'text',
            'content' => 'Hello, is this item still available in stock?'
        ]),
        makeRequest('Mark Conversation Messages as Read', 'POST', '/conversations/1/read', []),
        makeRequest('Mark Messages as Delivered', 'POST', '/conversations/1/delivered', [])
    ]
];

// 22. Notifications & Device Tokens
$collection['item'][] = [
    'name' => '22. Notifications & Device Tokens',
    'item' => [
        makeRequest('Get In-App Notifications List', 'GET', '/me/notifications'),
        makeRequest('Get Unread Notifications Count', 'GET', '/me/notifications/unread-count'),
        makeRequest('Register Push Device FCM/APNs Token', 'POST', '/me/devices/inst_dev_uuid_1001', [
            'device_type' => 'ios',
            'push_token' => 'fcm_mock_device_token_xyz9982'
        ]),
        makeRequest('Unregister Device Token', 'DELETE', '/me/devices/inst_dev_uuid_1001')
    ]
];

// 23. Moderation, Reporting & Blocking
$collection['item'][] = [
    'name' => '23. Moderation, Reporting & Blocking',
    'item' => [
        makeRequest('Submit a Moderation Report', 'POST', '/reports', [
            'target_type' => 'stream',
            'target_id' => 1,
            'reason' => 'inappropriate_content',
            'description' => 'Violates community guidelines'
        ]),
        makeRequest('Block a User', 'POST', '/me/blocked-users/3', []),
        makeRequest('Unblock a User', 'DELETE', '/me/blocked-users/3')
    ]
];

// 24. Media Uploads
$collection['item'][] = [
    'name' => '24. Media Uploads',
    'item' => [
        makeRequest('Upload Image or Media File', 'POST', '/uploads', [
            'file_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30'
        ])
    ]
];

$jsonOutput = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Write to root and public directories
file_put_contents(__DIR__ . '/../zaldoris_api_v1.postman_collection.json', $jsonOutput);
file_put_contents(__DIR__ . '/../public/zaldoris_api_v1.postman_collection.json', $jsonOutput);

echo "Successfully generated Postman Collection containing " . count($collection['item']) . " modules!\n";
