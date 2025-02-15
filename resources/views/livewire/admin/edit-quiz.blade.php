<?php


use App\Models\Quiz;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Mary\Traits\Toast;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use League\Csv\Writer;
use League\Csv\Reader;

new class extends Component
{
    use WithFileUploads;

    use Toast;

    // public $csvFile;

    public $uploadedFile;

    public Subject $subject;

    // This property holds an array of questions where each question has:
    // - content: the question text
    // - options: an array of option strings
    // - correct_answer: an integer index referring to the correct option
    public $questions = [];

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
        $this->loadQuiz();
    }

    /**
     * Define validation rules.
     */
    protected function rules()
    {
        return [
            'questions' => 'required|array|min:1',
            'questions.*.content' => 'required|string',
            'questions.*.options' => 'required|array|min:1',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct_answer' => 'required|integer|min:0',
            'uploadedFile' => 'nullable|file|mimes:csv|max:1024',
        ];
    }

    /**
     * Customize validation messages.
     */
    protected function messages()
    {
        return [
            'questions.required' => 'Please add at least one question.',
            'questions.min' => 'Please add at least one question.',
            'questions.*.content.required' => 'Please enter a question.',
            // 'questions.*.content.min' => 'Each question must be at least :min characters long.',
            'questions.*.options.required' => 'Please provide at least one option for each question.',
            'questions.*.options.min' => 'Please provide at least one option for each question.',
            'questions.*.options.*.required' => 'Please fill in each option.',
            'questions.*.correct_answer.required' => 'Please select the correct answer for each question.',
            'questions.*.correct_answer.integer' => 'The correct answer must be a valid option index.',
            'questions.*.correct_answer.min' => 'The correct answer must be a valid option index.',
            'uploadedFile.mimes' => 'The file must be a CSV file.',
            'uploadedFile.max' => 'The file size must not exceed 1MB.',
        ];
    }


    public function uploadCSV()
    {
        $this->validate([
            'uploadedFile' => 'required|file|mimes:csv|max:1024',
        ]);

        try {
            $csv = Reader::createFromPath($this->uploadedFile->path());
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $newQuestions = [];
            $rowNumber = 1;

            foreach ($records as $record) {
                $rowNumber++;

                // Convert record to array for easier handling
                $row = array_values($record);

                // Validate minimum required fields
                if (count($row) < 4) { // Question + Correct Option + Number of Options + at least 1 option
                    $this->error('Error', "Row $rowNumber: Each row must have at least a question, correct option number, number of options, and at least one option.");
                    return;
                }

                $question = $row[0];
                $correctOption = (int)$row[1];
                $numOptions = (int)$row[2];

                // Extract options
                $options = array_slice($row, 3, $numOptions);

                // Validate number of options matches declared count
                if (count($options) !== $numOptions) {
                    $this->error('Error', "Row $rowNumber: Number of options doesn't match the declared count.");
                    $this->error('Error', "Expected Number of Options: " . $numOptions . "Found options " . count($options),  timeout: 9000);
                    return;
                }

                // Validate correct option number
                if ($correctOption < 1 || $correctOption > $numOptions) {
                    $this->error('Error', "Row $rowNumber: Correct option number must be between 1 and the number of options.");
                    return;
                }

                // Validate minimum options
                if (count($options) < 2) {
                    $this->error('Error', "Row $rowNumber: Each question must have at least 2 options.");
                    return;
                }


                $newQuestions[] = [
                    'content' => $question,
                    'options' => $options,
                    'correct_answer' => $correctOption - 1 // Convert to 0-based indexing
                ];
            }

            // dump($newQuestions);
            // dd($this->questions);
            $this->questions = array_merge($this->questions, $newQuestions);
            // [] = $newQuestions;
            // $this->success('Success', 'CSV file uploaded and processed successfully!');

        } catch (\Exception $e) {
            Log::error($e);
            $this->error('Error', 'Failed to process CSV file. Please ensure it matches the sample format.');
        }

        $this->uploadedFile = null; // Reset file input
    }

    public function downloadSample()
    {
        // $headers = [
        //     'Content-Type' => 'text/csv',
        //     'Content-Disposition' => 'attachment; filename="sample_quiz_questions.csv"',
        // ];

        // $csv = Writer::createFromString('');

        // Headers with dynamic options
        // $csv->insertOne(['Question', 'Correct Option Number', 'Number of Options', 'Options...']);
        $headers = ['Question', 'Correct Option Number', 'Number of Options', 'Options...'];

        $question1 = ['What is 2 + 2?', '2', '4', '3', '4', '5', '6'];
        $question2 = ['Which planet is closest to the Sun?', '2', '3', 'Venus', 'Mercury', 'Mars'];
        $question3 = ['True or False: Water boils at 100°C at sea level', '1', '2', 'True', 'False'];
        // Sample data with varying numbers of options
        // $csv->insertOne(['What is 2 + 2?', '2', '4', '3', '4', '5', '6']);
        // $csv->insertOne(['Which planet is closest to the Sun?', '2', '3', 'Venus', 'Mercury', 'Mars']);
        // $csv->insertOne(['True or False: Water boils at 100°C at sea level', '1', '2', 'True', 'False']);
        return response()->streamDownload(function () use ($headers, $question1, $question2, $question3): void {
            $output = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers and sample row
            fputcsv($output, $headers);
            fputcsv($output, $question1);
            fputcsv($output, $question2);
            fputcsv($output, $question3);

            fclose($output);
        }, 'questions_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=questions_template.csv',
        ]);

        // return response($csv->toString(), 200, $headers);
    }



    /**
     * Customize attribute names for error messages.
     */
    protected function validationAttributes()
    {
        return [
            'questions.*.content' => 'question content',
            'questions.*.options' => 'question options',
            'questions.*.correct_answer' => 'correct answer',
        ];
    }

    public function downloadCurrentQuestions()
    {
        $sName = $this->subject->name;
        $headers = ['Question', 'Correct Option Number', 'Number of Options', 'Options...'];
        return response()->streamDownload(function () use ($headers): void {
            $output = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers and sample row
            fputcsv($output, $headers);
            foreach ($this->questions as $question) {
                $row = [
                    $question['content'],
                    $question['correct_answer'] + 1, // Convert to 1-based indexing
                    count($question['options']),
                ];

                // Add all options
                $row = array_merge($row, $question['options']);
                fputcsv($output, $row);
            }
            fclose($output);
        }, $sName . 'questions.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $sName . 'questions.csv',
        ]);
    }

    /**
     * Loads quiz data from the subject.
     */
    public function loadQuiz()
    {
        $this->title = $this->subject->name;
        $this->description = 'Test Your Skills';
        $this->timeLimit = $this->subject->loadCount('questions')->calculateTimeLimit();

        // Map subject questions to the component's questions array
        $this->questions = $this->subject->questions->map(function ($question) {
            return [
                'content'        => $question->content,
                'options'        => $question->options,
                'correct_answer' => $question->correct_answer,
            ];
        })->toArray();
    }

    /**
     * Adds a new question.
     * Note: We initialize with two empty options.
     */
    public function addQuestion()
    {
        $this->questions[] = [
            'content'        => '',
            'options'        => ['', ''], // Default to two options.
            'correct_answer' => 0,
        ];
    }

    /**
     * Adds a new option to a given question.
     */
    public function addOption($questionIndex)
    {
        $this->questions[$questionIndex]['options'][] = '';
    }

    /**
     * Removes an option from a given question.
     */
    public function removeOption($questionIndex, $optionIndex)
    {
        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
    }

    /**
     * Removes an entire question.
     */
    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    /**
     * Called when the form is submitted.
     * It validates the data and then updates the quiz.
     */
    public function saveQuiz()
    {
        // Validate the data before updating
        $this->validate();

        return $this->updateQuiz();
    }

    #[On('model-updated')]
    public function refreshModel()
    {
        $this->subject->refresh();
        // $this->dispatch('model-updated', model: $this->model);
    }

    /**
     * Updates the quiz by deleting old questions and saving the new ones.
     */
    public function updateQuiz()
    {
        if ($this->subject) {
            Gate::authorize('update', $this->subject);

            // Delete all existing questions
            $this->subject->questions()->delete();

            // Create new question records from validated data
            foreach ($this->questions as $questionData) {
                $question = new Question([
                    'content'        => $questionData['content'],
                    'options'        => $questionData['options'],
                    'correct_answer' => $questionData['correct_answer'],
                ]);
                $this->subject->questions()->save($question);
            }

            $this->success('Updated', 'Quiz updated successfully!');
        } else {
            $this->error('Failed', 'Quiz update failed!');
        }
    }

    /**
     * Passes data to the view.
     */
    public function with(): array
    {
        return [
            'questions' => $this->questions,
        ];
    }
}; ?>

<div class="max-w-4xl p-6 mx-auto mt-10  rounded-lg shadow-lg">
    <h2 class="mb-6 text-3xl font-bold ">Edit Quiz </h2>
    <h2 class="mb-6 text-2xl font-semibold ">Subject: {{ $this->subject->name }}</h2>

    <div class="p-3">
        <div class="m-4">
            <livewire:edit-model-attribute class="border rounded-md" type="number" attribute="questions_per_quiz" :model="$this->subject" rules='integer|min:1' />
        </div>
        <div class="m-4">
            <livewire:edit-model-attribute class="border rounded-md" type="number" attribute="mins_per_question" :model="$this->subject" rules='numeric|min:0.1' />
        </div>
    </div>

    <div class="mb-6 p-4  rounded-lg">

        <div class="flex items-center space-x-4 mb-4">
            <button wire:click="downloadSample" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                Download Sample CSV
            </button>

            <button wire:click="downloadCurrentQuestions" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                Export Current Questions
            </button>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium ">
                Upload CSV File
            </label>
            <div class="flex items-center space-x-4">
                <input type="file" wire:model="uploadedFile" class="block w-full text-sm 
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100
                ">
                <button wire:click="uploadCSV" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">
                    Upload
                </button>
            </div>
            @error('uploadedFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <form wire:submit.prevent="saveQuiz" class="space-y-6">





        <div class="space-y-4">
            @if(count($questions) < 1)
                <p class="text-gray-700 dark:text-gray-300">There are no Questions</p>
                @else
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Questions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Questions Count: {{ count($questions) }}</p>

                @foreach ($questions as $index => $question)
                <div class="p-4 rounded-md shadow bg-gray-50 dark:bg-gray-800">
                    <!-- Question Input -->
                    <input
                        type="text"
                        wire:model.live="questions.{{ $index }}.content"
                        placeholder="Enter your question here"
                        class="block w-full mb-3 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-600 focus:ring-opacity-50">

                    <div class="mb-3 space-y-2">
                        @foreach ($question['options'] as $optionIndex => $option)
                        <div class="flex items-center">
                            <input
                                type="text"
                                wire:model.live="questions.{{ $index }}.options.{{ $optionIndex }}"
                                placeholder="Option {{ $optionIndex + 1 }}"
                                class="block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                            <button
                                type="button"
                                wire:click="removeOption({{ $index }}, {{ $optionIndex }})"
                                class="px-2 py-1 ml-2 text-white transition duration-150 ease-in-out bg-red-500 rounded hover:bg-red-600">
                                Remove
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between">
                        <!-- Correct Answer Dropdown -->
                        <select
                            wire:model.live="questions.{{ $index }}.correct_answer"
                            class="block w-1/2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                            <option value="">Select correct answer</option>
                            @foreach ($question['options'] as $optionIndex => $option)
                            <option value="{{ $optionIndex }}">
                                Option {{ $optionIndex + 1 }} ({{ $question['options'][$optionIndex] }}) is correct
                            </option>
                            @endforeach
                        </select>

                        <!-- Remove Question Button -->
                        <button
                            type="button"
                            wire:click="removeQuestion({{ $index }})"
                            class="px-3 py-1 text-white transition duration-150 ease-in-out bg-red-500 rounded hover:bg-red-600">
                            Remove Question
                        </button>
                    </div>

                    <!-- Add Option Button -->
                    <button
                        type="button"
                        wire:click="addOption({{ $index }})"
                        class="px-4 py-2 mt-2 text-white transition duration-150 ease-in-out bg-green-500 rounded hover:bg-green-600">
                        Add Option
                    </button>
                </div>
                @endforeach
                @endif
        </div>


        <div class="flex items-center justify-between">
            <button type="button" wire:click="addQuestion" class="px-4 py-2 text-white transition duration-150 ease-in-out bg-green-500 rounded hover:bg-green-600">
                Add Question
            </button>
            <button type="submit" class="px-6 py-2 text-white transition duration-150 ease-in-out bg-blue-500 rounded hover:bg-blue-600">
                {{ $subject && $subject->exists ? 'Update Quiz' : 'Create Quiz' }}
            </button>
        </div>
    </form>
    @error('questions.0.content') <span class="error">{{ $message }}</span> @enderror
</div>