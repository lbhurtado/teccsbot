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
            ['title' => 'Activity - Read the manual', 'rank' => '1'],
            ['title' => 'Activity - Recruit 15 workers', 'rank' => '2'],
            ['title' => 'Activity - Recruit 15 staff', 'rank' => '3'],
        ],
        'staff' => [
            ['title' => 'Activity - Read the manual', 'rank' => '1'],
            ['title' => 'Activity - Recruit 15 voters', 'rank' => '2'],
        ],
        'subscriber' => [
            ['title' => 'Activity - Read the manual', 'rank' => '1'],
            ['title' => 'Activity - Recruit 15 voters', 'rank' => '2'],
        ],
        'worker' => [
            ['title' => 'Activity - Read the manual', 'rank' => '1', 'instructions' => 'Start from page 1.'],
            ['title' => 'Activity - Register', 'rank' => '2'],
            ['title' => 'Activity - Verify BEI Composition', 'rank' => '3'],
            ['title' => 'Witness - Ballot Box Seal', 'rank' => '4'],
            ['title' => 'Witness - Zero Votes Print-Out', 'rank' => '5'],
            ['title' => 'Activity - Vote', 'rank' => '6'],
            ['title' => 'Witness - Election Return Print-Out', 'rank' => '6'],
            ['title' => 'Witness - Election Return Trasmission', 'rank' => '7'],
            ['title' => 'Activity - Poll Count', 'rank' => '8'],
        ],
    ],
    'surveys' => [
        [
            'code' => 'Mayor',
            'question' => "Who will you most likely vote?",
            'answers' => [
                'Estrada',
                'Lim',
                'Atienza',
                'Moreno',
            ],
        ],
        [
            'code' => 'Issue',
            'question' => "What is the problem?",
            'answers' => [
                'Traffic',
                'Jobs',
                'Crime',
            ],
        ],
        [
            'code' => 'Color',
            'question' => "What is you favorite color?",
            'answers' => [
                'Red',
                'Orange',
                'Yellow',
                'Green',
            ],
        ],
    ],
];
