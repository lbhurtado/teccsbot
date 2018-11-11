<?php

use Illuminate\Validation\Rule;

return [
   'links' => [ 
        'messenger' => [
            'Telegram' => 'http://t.me/grassroots_bot',
            'Facebook' => 'http://m.me/dyagwarbot',
        ],
    ],
    'rules' => [
        'age' => 'integer|min:18|max:100',
        'sex' => 'in:male,female',
        'gender' => 'in:male,female',
        'status' => 'in:single,married,separated,widowed',
        'eyes' => 'in:brown,blue,green,others',
        'hair' => 'in:black,brown,red,others',
        'education' => 'in:elementary,high-school,college,post-grad',
        'zip' => 'digits:4',
    ],
    'keywords' => [
        'subscriber' => [
        ],
        'watcher' => [
        ],
        'worker' => [
        ],
        'operator' => [
        ],
        'staff' => [
        ],
        'admin' => [
        ],
    ],
    'tasks' => [
        'test' => [
            ['title' => 'Task 1'],
            ['title' => 'Task 2'],
            ['title' => 'Task 3'],
        ],
        'admin' => [
            ['title' => 'Activity - Recruit 15 operators'],
        ],
        'operator' => [
            ['title' => 'Activity - Read the manual'],
            ['title' => 'Activity - Recruit 15 workers'],
            ['title' => 'Activity - Recruit 15 staff'],
        ],
        'staff' => [
            ['title' => 'Activity - Read the manual'],
            ['title' => 'Activity - Recruit 15 voters'],
        ],
        'subscriber' => [
            ['title' => 'Activity - Read the manual'],
            ['title' => 'Activity - Recruit 15 voters'],
        ],
        'worker' => [
            ['title' => 'Activity - Read the manual'],
            ['title' => 'Activity - Register'],
            ['title' => 'Activity - Verify BEI Composition'],
            ['title' => 'Witness - Ballot Box Seal'],
            ['title' => 'Witness - Zero Votes Print-Out'],
            ['title' => 'Activity - Vote'],
            ['title' => 'Witness - Election Return Print-Out'],
            ['title' => 'Witness - Election Return Trasmission'],
            ['title' => 'Activity - Poll Count'],
        ],
    ],
];
