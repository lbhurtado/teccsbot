<?php

use App\Answer;
use App\Question;
use Illuminate\Database\Seeder;

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
                'question' => 'Who will you vote for in the 2019 elections?',
                'points' => '10',
                'answers' => [
                    ['text' => 'Erap Estrada', 'correct_one' => true],
                    ['text' => 'Isko Moreno', 'correct_one' => false],
                    ['text' => 'Lito Atienza', 'correct_one' => false],
                    ['text' => 'Alfredo Lim', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'What is the most important issue?',
                'points' => '10',
                'answers' => [
                    ['text' => 'Crime', 'correct_one' => true],
                    ['text' => 'Corruption', 'correct_one' => false],
                    ['text' => 'Environment', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'What is your problem?',
                'points' => '20',
                'answers' => [
                    ['text' => 'Health', 'correct_one' => false],
                    ['text' => 'Labor', 'correct_one' => false],
                    ['text' => 'Education', 'correct_one' => true],
                ],
            ],
        ]);
    }
}
