<?php

return [
    'stripeAccounts' => [
        'inputs' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
            ],
            'account_id' => [
                'label' => 'Account id',
                'placeholder' => 'Account id',
            ],
            'user_id' => [
                'label' => 'Owner',
                'placeholder' => 'User id',
            ],
        ],
        'filament' => [
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'account_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'user_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
        ],
    ],
    'users' => [
        'inputs' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
            ],
            'email' => [
                'label' => 'Email',
                'placeholder' => 'Email',
            ],
            'password' => [
                'label' => 'Password',
                'placeholder' => 'Password',
            ],
        ],
        'filament' => [
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'email' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'password' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
        'itemTitle' => 'User',
        'collectionTitle' => 'Users',
    ],
    'stripeProducts' => [
        'inputs' => [
            'account_id' => [
                'label' => 'Account id',
                'placeholder' => 'Account id',
            ],
            'seller_account_id' => [
                'label' => 'Seller account id',
                'placeholder' => 'Seller account id',
            ],
            'sp_connection_name' => [
                'label' => 'Sp connection name',
                'placeholder' => 'Sp connection name',
            ],
            'sp_ctx' => [
                'label' => 'Sp ctx',
                'placeholder' => 'Sp ctx',
            ],
            'statement_descriptor' => [
                'label' => 'Statement descriptor',
                'placeholder' => 'Statement descriptor',
            ],
        ],
        'filament' => [
            'account_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'seller_account_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'active' => [
                'helper_text' => '',
                'label' => '',
            ],
            'livemode' => [
                'helper_text' => '',
                'label' => '',
            ],
            'created' => [
                'helper_text' => '',
                'label' => '',
            ],
            'updated' => [
                'helper_text' => '',
                'label' => '',
            ],
            'description' => [
                'helper_text' => '',
                'label' => '',
            ],
            'images' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'metadata' => [
                'helper_text' => '',
                'label' => '',
            ],
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'package_dimensions' => [
                'helper_text' => '',
                'label' => '',
            ],
            'shippable' => [
                'helper_text' => '',
                'label' => '',
            ],
            'sp_connection_name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'sp_ctx' => [
                'helper_text' => '',
                'label' => '',
            ],
            'statement_descriptor' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'type' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'unit_label' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'url' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'price' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'price_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
    ],
    'collections' => [
        'itemTitle' => 'Collection',
        'collectionTitle' => 'Collections',
        'inputs' => [
            'store_id' => [
                'label' => 'Store id',
                'placeholder' => 'Store id',
            ],
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
            ],
            'visible' => [
                'label' => 'Visible',
                'placeholder' => 'Visible',
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
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'visible' => [
                'helper_text' => '',
                'label' => '',
            ],
        ],
    ],
    'images' => [
        'itemTitle' => 'Image',
        'collectionTitle' => 'Images',
        'inputs' => [
            'filename' => [
                'label' => 'Filename',
                'placeholder' => 'Filename',
            ],
            'mime_type' => [
                'label' => 'Mime type',
                'placeholder' => 'Mime type',
            ],
            'public_url' => [
                'label' => 'Public url',
                'placeholder' => 'Public url',
            ],
            'store_id' => [
                'label' => 'Store id',
                'placeholder' => 'Store id',
            ],
        ],
        'filament' => [
            'filename' => [
                'helper_text' => '',
                'label' => '',
            ],
            'mime_type' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'public_url' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'store_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
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
            'main_variant_id' => [
                'label' => 'Main variant id',
                'placeholder' => 'Main variant id',
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
        ],
        'filament' => [
            'store_id' => [
                'helper_text' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
                'label' => '',
            ],
            'main_variant_id' => [
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
        ],
    ],
    'stores' => [
        'itemTitle' => 'Store',
        'collectionTitle' => 'Stores',
        'inputs' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Name',
            ],
            'stripe_account_id' => [
                'label' => 'Stripe account id',
                'placeholder' => 'Stripe account id',
            ],
        ],
        'filament' => [
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'stripe_account_id' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
    ],
    'storeUser' => [
        'itemTitle' => 'Store User',
        'collectionTitle' => 'Store User',
        'inputs' => [
            'role' => [
                'label' => 'Role',
                'placeholder' => 'Role',
            ],
        ],
        'filament' => [
            'role' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
    ],
];
