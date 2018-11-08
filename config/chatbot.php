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
];
