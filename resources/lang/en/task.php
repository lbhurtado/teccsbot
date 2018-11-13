<?php

return [
	'introduction' => "You have :count available tasks.",
	// 'introduction' => "You have :count available tasks.\n□ not accepted ■ accepted\n○ not started • started",
	'choose' => [
		'task' => "Choose a task:",
		'error' => "Error! Something is wrong in choosing a task.",
	],
	'read' => [
		'optional' => "Read the instructions?",
		'error' => "Error!, Something is wrong in reading the instructions.",
		'instructions' => "Instructions:\n:instructions",
		'continue' => "Continue?",
		'affirmative' => "Yes",
		'negative' => 'No',
	],
	'accept' => [
		'question' => "Do you accept this task - \":title\"?",
		'error' => "Error! Something is wrong in accepting a task.",
		'affirmative' => "Yes, please.",
		'negative' => 'No, thanks.',
		'accepted' => "You have accepted the task  - \":title\".",
		'declined' => "You have declined the task - \":title\".",
	],
	'start' => [
		'question' => "Do you want to start working on the task - \":title\"?",
		'error' => 'Error! Something is wrong in starting the task.',
		'affirmative' => "Sure.",
		'negative' => "Not yet.",
		'accepted' => "You have started the task - \":title\".",
		'declined' => "You have not started the task yet - \":title\".",
	],
	'end' => [
		'question' => "Are you finished with the task - \":title\"?",
		'error' => "Error!, Something wrong with in finishing the task.",
		'affirmative' => "Yes, it's finished.",
		'negative' => "No, not yet.",
		'abandon' => "Abandon...",
		'completed' => "You are finished with the task - \":title\".",
		'deferred' => "You are not yet finished with the task - \":title\".",
		'abandoned' => "You have abandoned the task - \":title\".",
	],
	'finished' => 'Done.',
];