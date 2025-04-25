<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class OpenAIController extends Controller
{
    public function summarize(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answers' => 'required|array',
        ]);

        $question = $request->input('question');
        $answers = $request->input('answers');

        $combinedAnswers = implode("\n- ", $answers);
        $prompt = "Summarize the following answers in relation to the question: \"$question\"\n\nAnswers:\n- $combinedAnswers";

        $ai = new OpenAIService();
        $summary = $ai->generateChatResponse($prompt);

        return response()->json(['summary' => $summary]);
    }
}
