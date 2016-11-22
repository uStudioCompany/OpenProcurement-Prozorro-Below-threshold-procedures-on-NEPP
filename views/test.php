<?php
$data = [
    'procurementMethod' => 'open',
    'numberOfBids' => 1,
    'awardPeriod' => [
        'startDate' => '2015-10-22T15:44:45.645893+03:00',
        'endDate' => '2015-10-22T15:44:47.812731+03:00',],
    'enquiryPeriod' => [
        'startDate' => '2015-10-21T15:42:44.311854+03:00',
        'endDate' => '2015-10-22T15:42:44.311854+03:00',
    ],
    'submissionMethod' => 'electronicAuction',
    'procuringEntity' => [
        'contactPoint' => [
            'name' => 'Name',
            'telephone' => '0440000000',],
        'identifier' => [
            'scheme' => 'UA-EDR',
            'id' => '00000000',],
        'name' => 'Name',
        'address' => [
            'countryName' => 'Україна',],],
    'id' => 'e456fcb5a8b447c988173ef62b1eb237',
    'title' => '[ТЕСТУВАННЯ] Title',
    'lots' => [
        0 => [
            'status' => 'active',
            'description' => 'lot description',
            'title' => 'lot title',
            'minimalStep' => [
                'currency' => 'UAH',
                'amount' => 100,
                'valueAddedTaxIncluded' => true,],
            'value' => [
                'currency' => 'UAH',
                'amount' => 100000,
                'valueAddedTaxIncluded' => true,
            ],
            'id' => 'c22dd2ec252c42908e73d3ca83861b3c',
        ],
        1 => [
            /* ... */
            'id' => '9da8cc15a4294485b0981574d761249c',],
        2 => [
            /* ... */
            'id' => '4ef0a1377a18429eaa49afac04e09d8f',],
        3 => [
            /* ... */
            'id' => '145753cc992d40d89594566fc3902b29',],
    ],
    'tenderID' => 'UA-2015-10-21-000002',
    'dateModified' => '2015-10-23T15:44:49.129776+03:00',
    'status' => 'active.awarded',
    'tenderPeriod' => [
        'startDate' => '2015-10-22T15:42:44.311854+03:00',
        'endDate' => '2015-10-22T15:44:44.311854+03:00',],
    'contracts' => [
        0 => [
            'status' => 'pending',
            'awardID' => 'e96a95e396714f4ca0bde21fba9198a8',
            'id' => '00438a0cac04464d9c00cf221c21ed36',],
    ],
    'title_en' => '[TESTING] ',
    'awards' => [
        0 => [
            'status' => 'active',
            'lotID' => 'c22dd2ec252c42908e73d3ca83861b3c',
            'complaintPeriod' => [
                'startDate' => '2015-10-22T15:44:45.645893+03:00',
                'endDate' => '2015-10-23T15:44:47.084653+03:00',],
            'suppliers' => [
                0 => [
                    'contactPoint' => [
                        'name' => 'Name',
                        'telephone' => '0440000000',],
                    'identifier' => [
                        'scheme' => 'UA-EDR',
                        'id' => '00000000',],
                    'name' => 'Name',
                    'address' => [
                        'countryName' => 'Україна',],
                ],
            ],
            'bid_id' => '19a4eafb17b945f2b4844eac9652dd3f',
            'value' => [
                'currency' => 'UAH',
                'amount' => 99999.990000000005,
                'valueAddedTaxIncluded' => true,],
            'date' => '2015-10-22T15:44:45.646543+03:00',
            'id' => 'e96a95e396714f4ca0bde21fba9198a8',],
        1 => [
            /* ... */
            'id' => '61a12407612a4ceeb13c4c9d36263656',],
    ],
    'minimalStep' => [
        'currency' => 'UAH',
        'amount' => 100,
        'valueAddedTaxIncluded' => true,],
    'items' => [
        0 => [
            'relatedLot' => 'c22dd2ec252c42908e73d3ca83861b3c',
            'description' => 'description',
            'classification' => [
                'scheme' => 'CPV',
                'description' => 'Cartons',
                'id' => '44617100-9',
            ],
            'additionalClassifications' => [
                0 => [
                    'scheme' => 'ДКПП',
                    'id' => '17.21.1',
                    'description' => 'description',
                ],
            ],
            'id' => '123456',
            'unit' => [
                'code' => '44617100-9',
                'name' => 'item',
            ],
            'quantity' => 5,
        ],
        1 => [
            'relatedLot' => '9da8cc15a4294485b0981574d761249c',
            /* ... */
            'id' => '123456',],
    ],
    'bids' => [// ставки
        0 => [
            'date' => '2015-10-22T15:43:59.337040+03:00',
            'id' => '19a4eafb17b945f2b4844eac9652dd3f',
            'lotValues' => [
                0 => [
                    'relatedLot' => 'c22dd2ec252c42908e73d3ca83861b3c',
                    'date' => '2015-10-22T15:43:59.336032+03:00',
                    'value' => [
                        'currency' => 'UAH',
                        'amount' => 99999.990000000005,
                        'valueAddedTaxIncluded' => true,],
                ],
                1 => [
                    /* .. */],
            ],
            'tenderers' => [
                0 => [
                    'contactPoint' => [
                        'name' => 'Name',
                        'telephone' => '0440000000',],
                    'identifier' => [
                        'scheme' => 'UA-EDR',
                        'id' => '00000000',],
                    'name' => 'Name',
                    'address' => [
                        'countryName' => 'Україна',],
                ],
            ],
        ],
    ],
    'cancellations' => [
        0 => [
            'status' => 'active',
            'relatedLot' => '4ef0a1377a18429eaa49afac04e09d8f',
            'reason' => 'reason',
            'date' => '2015-10-22T15:44:00.341933+03:00',
            'cancellationOf' => 'lot',
            'id' => '14104f80e0484852b752a3a86aa78c07',],
    ],

    'value' => [
        'currency' => 'UAH',
        'amount' => 400000,
        'valueAddedTaxIncluded' => true,],
    'mode' => 'test',


    'title_ru' => '[ТЕСТИРОВАНИЕ] ',
    'awardCriteria' => 'lowestCost',
];