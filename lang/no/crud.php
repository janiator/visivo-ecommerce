<?php

declare(strict_types=1);

return [
    'stripeAccounts' => [
        'inputs' => [
            'name' => [
                'label' => 'Navn',
                'placeholder' => 'Navn',
            ],
            'account_id' => [
                'label' => 'Konto-ID',
                'placeholder' => 'Konto-ID',
            ],
            'user_id' => [
                'label' => 'Eier',
                'placeholder' => 'Bruker-ID',
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
                'label' => '',
                'description' => '',
                'loading_message' => '',
                'no_result_message' => '',
                'search_message' => '',
            ],
        ],
    ],
    'users' => [
        'inputs' => [
            'name' => [
                'label' => 'Navn',
                'placeholder' => 'Navn',
            ],
            'email' => [
                'label' => 'E-post',
                'placeholder' => 'E-post',
            ],
            'password' => [
                'label' => 'Passord',
                'placeholder' => 'Passord',
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
        'itemTitle' => 'Bruker',
        'collectionTitle' => 'Brukere',
    ],
    'stripeProducts' => [
        'inputs' => [
            'account_id' => [
                'label' => 'Konto-ID',
                'placeholder' => 'Konto-ID',
            ],
            'seller_account_id' => [
                'label' => 'Selgerkonto-ID',
                'placeholder' => 'Selgerkonto-ID',
            ],
            'sp_connection_name' => [
                'label' => 'SP Tilkoblingsnavn',
                'placeholder' => 'SP Tilkoblingsnavn',
            ],
            'sp_ctx' => [
                'label' => 'SP Kontekst',
                'placeholder' => 'SP Kontekst',
            ],
            'statement_descriptor' => [
                'label' => 'Kontoutskriftbeskrivelse',
                'placeholder' => 'Kontoutskriftbeskrivelse',
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
        'itemTitle' => 'Samling',
        'collectionTitle' => 'Samlinger',
        'inputs' => [
            'store_id' => [
                'label' => 'Butikk-ID',
                'placeholder' => 'Butikk-ID',
            ],
            'name' => [
                'label' => 'Navn',
                'placeholder' => 'Navn',
            ],
            'visible' => [
                'label' => 'Synlig',
                'placeholder' => 'Synlig',
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
        'itemTitle' => 'Bilde',
        'collectionTitle' => 'Bilder',
        'inputs' => [
            'filename' => [
                'label' => 'Filnavn',
                'placeholder' => 'Filnavn',
            ],
            'mime_type' => [
                'label' => 'Mime-type',
                'placeholder' => 'Mime-type',
            ],
            'public_url' => [
                'label' => 'Offentlig URL',
                'placeholder' => 'Offentlig URL',
            ],
            'store_id' => [
                'label' => 'Butikk-ID',
                'placeholder' => 'Butikk-ID',
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
        'itemTitle' => 'Produkt',
        'collectionTitle' => 'Produkter',
        'section' => [
            'details' => 'Produktdetaljer',
        ],
        'inputs' => [
            'store_id' => [
                'label' => 'Butikk-ID',
                'placeholder' => 'Butikk-ID',
            ],
            'main_variant_id' => [
                'label' => 'Hovedvariant-ID',
                'placeholder' => 'Hovedvariant-ID',
            ],
            'main_image' => [
                'label' => 'Hovedbilde',
            ],
            'gallery_images' => [
                'label' => 'Galleribilder',
            ],
            'status' => [
                'label' => 'Status',
                'placeholder' => 'Status',
                'options' => [
                    'published' => 'Publisert',
                    'draft' => 'Utkast',
                    'archived' => 'Arkivert',
                ],
            ],
            'name' => [
                'label' => 'Navn',
                'placeholder' => 'Navn',
            ],
            'type' => [
                'label' => 'Produkttype',
                'placeholder' => 'Produkttype',
                'options' => [
                    'physical' => 'Fysisk',
                    'digital' => 'Digital',
                    'service' => 'Tjeneste',
                    'subscription' => 'Abonnement',
                ],
            ],
            'description' => [
                'label' => 'Beskrivelse',
                'placeholder' => 'Beskrivelse',
            ],
            'price' => [
                'label' => 'Pris',
                'placeholder' => 'Pris',
            ],
            'short_description' => [
                'label' => 'Kort beskrivelse',
                'placeholder' => 'Kort beskrivelse',
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
    'product_variants' => [
        'section' => [
            'title' => 'Produktvarianter',
        ],
        'inputs' => [
            'name' => [
                'label' => 'Variantnavn',
                'placeholder' => 'Variantnavn',
            ],
            'price' => [
                'label' => 'Variantpris (NOK)',
                'placeholder' => 'Variantpris',
            ],
            'grouping_attribute' => [
                'label' => 'Grupperingsattributt',
                'placeholder' => 'Grupperingsattributt',
            ],
            'available_stock' => [
                'label' => 'Tilgjengelig beholdning',
                'placeholder' => 'Tilgjengelig beholdning',
            ],
            'committed_stock' => [
                'label' => 'Forpliktet beholdning',
                'placeholder' => 'Forpliktet beholdning',
            ],
            'unavailable_stock' => [
                'label' => 'Utilgjengelig beholdning',
                'placeholder' => 'Utilgjengelig beholdning',
            ],
            'incoming_stock' => [
                'label' => 'Inngående beholdning',
                'placeholder' => 'Inngående beholdning',
            ],
            'short_description' => [
                'label' => 'Kort beskrivelse',
                'placeholder' => 'Kort beskrivelse',
            ],
            'description' => [
                'label' => 'Beskrivelse',
                'placeholder' => 'Beskrivelse',
            ],
            'main_image' => [
                'label' => 'Hovedbilde',
                'placeholder' => 'Velg hovedbilde',
            ],
            'gallery_images' => [
                'label' => 'Galleri Bilder',
                'placeholder' => 'Last opp galleri bilder',
            ],
        ],
        'filament' => [
            'name' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'price' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'grouping_attribute' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'available_stock' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'committed_stock' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'unavailable_stock' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'incoming_stock' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'short_description' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'description' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'main_image' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
            'gallery_images' => [
                'helper_text' => '',
                'label' => '',
                'description' => '',
            ],
        ],
    ],
    'stores' => [
        'itemTitle' => 'Butikk',
        'collectionTitle' => 'Butikker',
        'inputs' => [
            'name' => [
                'label' => 'Navn',
                'placeholder' => 'Navn',
            ],
            'stripe_account_id' => [
                'label' => 'Stripe-konto-ID',
                'placeholder' => 'Stripe-konto-ID',
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
        'itemTitle' => 'Butikkbruker',
        'collectionTitle' => 'Butikkbruker',
        'inputs' => [
            'role' => [
                'label' => 'Rolle',
                'placeholder' => 'Rolle',
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
