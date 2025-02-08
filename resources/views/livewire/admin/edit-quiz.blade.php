<?php


use App\Models\Quiz;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

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
        ];
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

<div class="max-w-4xl p-6 mx-auto mt-10 bg-white rounded-lg shadow-lg">
    <h2 class="mb-6 text-3xl font-bold text-gray-800">Edit Quiz </h2>
    <h2 class="mb-6 text-2xl font-semibold text-gray-900">Subject: {{ $this->subject->name }}</h2>

    <form wire:submit.prevent="saveQuiz" class="space-y-6">
       

        


        <div class="space-y-4">
            @if(count($questions)<1)
                <p>There are no Questions</p>

            @else
            <h3 class="text-xl font-semibold text-gray-800">Questions</h3>
            @foreach ($questions as $index => $question)
            <div class="p-4 rounded-md shadow bg-gray-50">
                <input type="text" wire:model.live="questions.{{ $index }}.content" placeholder="Enter your question here" class="block w-full mb-3 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                <div class="mb-3 space-y-2">
                    @foreach ($question['options'] as $optionIndex => $option)
                    <div class="flex items-center">
                        <input type="text" wire:model.live="questions.{{ $index }}.options.{{ $optionIndex }}" placeholder="Option {{ $optionIndex + 1 }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <button type="button" wire:click="removeOption({{ $index }}, {{ $optionIndex }})" class="px-2 py-1 ml-2 text-white transition duration-150 ease-in-out bg-red-500 rounded hover:bg-red-600">Remove</button>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between">
                    <select wire:model.live="questions.{{ $index }}.correct_answer" class="block w-1/2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Select correct answer</option>
                        @foreach ($question['options'] as $optionIndex => $option)
                        <option value="{{ $optionIndex }}">Option {{ $optionIndex + 1 }} ({{ $question['options'][$optionIndex] }}) is correct</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="removeQuestion({{ $index }})" class="px-3 py-1 text-white transition duration-150 ease-in-out bg-red-500 rounded hover:bg-red-600">Remove Question</button>
                </div>

                <button type="button" wire:click="addOption({{ $index }})" class="px-4 py-2 mt-2 text-white transition duration-150 ease-in-out bg-green-500 rounded hover:bg-green-600">
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