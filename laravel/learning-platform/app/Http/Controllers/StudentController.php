<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Cours;
use App\Models\StudentCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer la liste de tous les cours
        $courses = Cours::all();
        return response()->json(['courses' => $courses]);
    }
    /**
     * Leave a comment for a course.
     */
    public function leaveComment(Request $request)
    {
        // Valider les données du formulaire
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'cours_id' => 'required|exists:cours,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Créer un nouveau commentaire pour le cours
        $comment = Comment::create($validator->validated());

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    /**
     * Register for a course.
     */
    public function registerForCourse(Request $request)
    {
        // Valider les données du formulaire
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'cours_id' => 'required|exists:cours,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Vérifier si l'étudiant a déjà fait une demande pour ce cours
        $existingRequest = StudentCourse::where('user_id', $request->user_id)
            ->where('cours_id', $request->cours_id)
            ->first();

        if ($existingRequest) {
            return response()->json(['error' => 'You have already requested to join this course'], 400);
        }

        // Créer une nouvelle demande pour rejoindre le cours
        $request = StudentCourse::create([
            'user_id' => $request->user_id,
            'cours_id' => $request->cours_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request to join course sent successfully', 'request' => $request], 201);
    }
    /**
     * Filter and search for courses.
     */
    public function filterCourses(Request $request)
    {
        // Filtrer et chercher des cours
        $filteredCourses = Cours::where('title', 'like', '%' . $request->search . '%')
            ->orWhereHas('instructor', function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            })
            ->orWhereHas('category', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orWhere('price', 'like', '%' . $request->search . '%')
            ->orWhere('difficulty', 'like', '%' . $request->search . '%')
            ->get();

        return response()->json(['courses' => $filteredCourses]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
