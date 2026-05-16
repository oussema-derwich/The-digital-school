<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function getQuestions()
    {
        return response()->json(Question::orderBy('question_number')->get());
    }

    public function submitSurvey(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'answers' => 'required|array',
            'answers.*' => 'required'
        ]);

        // Vérifier que toutes les 20 questions ont une réponse
        $questions = Question::orderBy('question_number')->get();
        if (count($request->answers) !== $questions->count()) {
            return response()->json([
                'error' => 'Toutes les questions doivent être répondues'
            ], 422);
        }

        $token = Str::random(32);

        $surveyResponse = SurveyResponse::create([
            'email' => $request->email,
            'token' => $token,
            'answers' => $request->answers
        ]);

        return response()->json([
            'message' => 'Merci pour votre participation !',
            'token' => $token,
            'response_url' => "/responses/{$token}"
        ]);
    }

    public function getResponse($token)
    {
        $response = SurveyResponse::where('token', $token)->first();

        if (!$response) {
            return response()->json(['error' => 'Réponse non trouvée'], 404);
        }

        $questions = Question::orderBy('question_number')->get();

        return response()->json([
            'response' => $response,
            'questions' => $questions
        ]);
    }

    public function getAllResponses()
    {
        return response()->json(SurveyResponse::with('')->orderBy('created_at', 'desc')->get());
    }

    public function getStatistics()
    {
        $responses = SurveyResponse::all();
        $questions = Question::orderBy('question_number')->get();

        $statistics = [];

        // Statistiques pour les Pie Charts (questions 6, 7, 10)
        $pieChartQuestions = [6, 7, 10];
        foreach ($pieChartQuestions as $questionNumber) {
            $question = $questions->where('question_number', $questionNumber)->first();
            if ($question && $question->type === 'A') {
                $stats = [];
                foreach ($question->options as $option) {
                    $count = $responses->filter(function ($response) use ($questionNumber, $option) {
                        return isset($response->answers[$questionNumber]) && 
                               $response->answers[$questionNumber] === $option;
                    })->count();
                    $stats[$option] = $count;
                }
                $statistics['pie_charts'][$questionNumber] = [
                    'question' => $question,
                    'data' => $stats
                ];
            }
        }

        // Statistiques pour le Radar Chart (questions 11-15)
        $radarData = [];
        for ($i = 11; $i <= 15; $i++) {
            $question = $questions->where('question_number', $i)->first();
            if ($question && $question->type === 'C') {
                $average = $responses->avg(function ($response) use ($i) {
                    return isset($response->answers[$i]) ? (int)$response->answers[$i] : 0;
                });
                $radarData[] = [
                    'question' => $question->title,
                    'average' => round($average, 2)
                ];
            }
        }
        $statistics['radar_chart'] = $radarData;

        return response()->json($statistics);
    }
}