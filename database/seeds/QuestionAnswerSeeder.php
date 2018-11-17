<?php

use Illuminate\Database\Seeder;
use App\Question;
use App\Answer;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Question::truncate();
        Answer::truncate();
        $questionAndAnswers = $this->getData();

        $questionAndAnswers->each(function ($question) {
            $createdQuestion = Question::create([
                'text' => $question['question'],
                'points' => $question['points'],
            ]);

            collect($question['answers'])->each(function ($answer) use ($createdQuestion) {
                Answer::create([
                    'question_id' => $createdQuestion->id,
                    'text' => $answer['text'],
                    'correct_one' => $answer['correct_one'],
                ]);
            });
        });
    }

	private function getData()
	{
	    return collect([
	        [
	            'question' => 'Who created Laravel?',
	            'points' => '5',
	            'answers' => [
	                ['text' => 'Christoph Rumpel', 'correct_one' => false],
	                ['text' => 'Jeffrey Way', 'correct_one' => false],
	                ['text' => 'Taylor Otwell', 'correct_one' => true],
	            ],
	        ],
	        [
	            'question' => 'Which of the following is a Laravel product?',
	            'points' => '10',
	            'answers' => [
	                ['text' => 'Horizon', 'correct_one' => true],
	                ['text' => 'Sunset', 'correct_one' => false],
	                ['text' => 'Nightfall', 'correct_one' => true],
	            ],
	        ],
	    ]);
	}
}
