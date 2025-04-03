<?php

return [
    'orders' => [
        'inputs' => [
            'store_id' => [
                'label' => 'Store id',
                'placeholder' => 'Store id',
            ],
            'customer_id' => [
                'label' => 'Customer id',
                'placeholder' => 'Customer id',
            ],
            'stripe_order_id' => [
                'label' => 'Stripe order id',
                'placeholder' => 'Stripe order id',
            ],
            'payment_intent' => [
                'label' => 'Payment intent',
                'placeholder' => 'Payment intent',
            ],
            'status' => [
                'label' => 'Status',
                'placeholder' => 'Status',
            ],
            'subtotal' => [
                'label' => 'Subtotal',
                'placeholder' => 'Subtotal',
            ],
            'total_amount' => [
                'label' => 'Total amount',
                'placeholder' => 'Total amount',
            ],
            'currency' => [
                'label' => 'Currency',
                'placeholder' => 'Currency',
            ],
            'shipping_address' => [
                'label' => 'Shipping address',
                'placeholder' => 'Shipping address',
            ],
            'billing_address' => [
                'label' => 'Billing address',
                'placeholder' => 'Billing address',
            ],
            'metadata' => [
                'label' => 'Metadata',
                'placeholder' => 'Metadata',
            ],
        ],
        'filament' => [
            'store_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
            'customer_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
            'stripe_order_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'payment_intent' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'status' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'subtotal' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'total_amount' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'currency' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'shipping_address' => [
                'helper_text' => '',
                'label' => '',
            ],
            'billing_address' => [
                'helper_text' => '',
                'label' => '',
            ],
            'metadata' => [
                'helper_text' => '',
                'label' => '',
            ],
        ],
    ],
    'orderItems' => [
        'filament' => [
            'product_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'product_variant_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'quantity' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'unit_price' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'total_price' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
    ],
    'products' => [
        'itemTitle' => 'Product',
        'collectionTitle' => 'Products',
        'inputs' => [
            'store_id' => [
                'label' => 'Store id',
                'placeholder' => 'Store id',
            ],
            'status' => [
                'label' => 'Status',
                'placeholder' => 'Status',
            ],
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
            ],
            'type' => [
                'label' => 'Type',
                'placeholder' => 'Type',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Description',
            ],
            'price' => [
                'label' => 'Price',
                'placeholder' => 'Price',
            ],
            'short_description' => [
                'label' => 'Short description',
                'placeholder' => 'Short description',
            ],
            'stripe_product_id' => [
                'label' => 'Stripe product id',
                'placeholder' => 'Stripe product id',
            ],
            'deleted_at' => [
                'label' => 'Deleted at',
                'placeholder' => 'Deleted at',
            ],
            'metadata' => [
                'label' => 'Metadata',
                'placeholder' => 'Metadata',
            ],
        ],
        'filament' => [
            'store_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
            'status' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'type' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'description' => [
                'helper_text' => '',
                'label' => '',
            ],
            'price' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'short_description' => [
                'helper_text' => '',
                'label' => '',
            ],
            'stripe_product_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'deleted_at' => [
                'helper_text' => '',
                'label' => '',
            ],
            'metadata' => [
                'helper_text' => '',
                'label' => '',
            ],
        ],
    ],
    'collectionProduct' => [
        'itemTitle' => 'Collection Product',
        'collectionTitle' => 'Collection Product',
        'inputs' => [
            'collection_id' => [
                'label' => 'Collection id',
                'placeholder' => 'Collection id',
            ],
        ],
        'filament' => [
            'collection_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
        ],
    ],
];
