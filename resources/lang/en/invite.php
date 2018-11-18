<?php

return [
	'input' => [
		'code' => 'Choose downline:',
		'mobile' => 'Input :code mobile number:',	
		'telegram' => 'Yes, send to Telegram.',
		'facebook' => 'Yes, send to Facebook.',
		'no' => 'No, please repeat.',
		'verify' => 'Verify: role=:code, mobile=:mobile [Yes/No]?',
	],
	'introduction' => "You may invite downlines here. Enter /stop anywhere to break the loop.",
	'notification' => "Hi. :name invites you to join 'One on Juan with Manong Johnny' - A Virtual Talk Show. Click :url to enlist.",
	'accepted' => "Hi. :name accepted your invitation. - serbis.io",
	'processing' => "Please stand by - processing...",
	'sent' => "Invitation has been sent.",
	'fail' => "Something is wrong. Please notify the system administrator.",
	'survey' => [
		'info' => "You will be shown :count questions about Laravel. Every correct answer will reward you with a certain amount of points. Please keep it fair and don't use any help. All the best! 🍀",
		'fallback' => "Sorry, I did not get that. Please use the buttons.",
		'question' => "➡️ Question: :current/:count : :text",
		'finished' => "Finished 🏁",
	],
];