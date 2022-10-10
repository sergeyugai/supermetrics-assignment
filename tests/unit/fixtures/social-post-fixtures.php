<?php

return [
    'no posts' => [
        0,
        [],
        []
    ],
    'one month, one post' => [
        1.0,
        [
            '01/2022'
        ],
        [
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
        ]
    ],
    'one month, two posts' => [
        2.0,
        [
            '01/2022'
        ],
        [
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
        ]
    ],
    'two months, one post' => [
        1.0,
        [
            '01/2022', '02/2022'
        ],
        [
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_1',
                'created_time' => '2022-02-20T00:00:00+00:00',
            ],
        ],
    ],
    'non-integer averages (1.5) for stats' => [
        1.5,
        [
            '01/2022'
        ],
        [
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_2',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
        ]
    ],
    'non-integer averages (1.33) for stats' => [
        1.33,
        [
            '01/2022'
        ],
        [
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_1',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_2',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
            [
                'from_id' => 'user_3',
                'created_time' => '2022-01-20T00:00:00+00:00',
            ],
        ],
    ],
];
