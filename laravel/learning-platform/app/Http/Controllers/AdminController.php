<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Cours;
use App\Models\Feedback;
use App\Models\Instructor;
use App\Models\StudentCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
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
        // Valider les données du formulaire
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'instructor_id' => 'required|exists:instructors,id',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'difficulty' => 'required|string',
            'lesson_count' => 'required|integer',
            'quiz_count' => 'required|integer',
            'price' => 'required|numeric',
            'student_limit' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Créer un nouveau cours
        $course = Cours::create($validator->validated());

        return response()->json(['message' => 'Course added successfully', 'course' => $course], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Récupérer et afficher les détails d'un cours spécifique
        $course = Cours::find($id);
        return response()->json(['course' => $course]);
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
        // Valider les données du formulaire
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'instructor_id' => 'exists:instructors,id',
            'description' => 'string',
            'duration' => 'integer',
            'difficulty' => 'string',
            'lessons_count' => 'integer',
            'quizzes_count' => 'integer',
            'price' => 'numeric',
            'category_id' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Mettre à jour les détails du cours spécifié
        $course = Cours::find($id);
        $course->update($validator->validated());

        return response()->json(['message' => 'Course updated successfully', 'course' => $course]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprimer un cours spécifique
        $course = Cours::find($id);
        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }


    // CRUD pour les catégories

    public function showCategory()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $category = Category::create($validator->validated());

        return response()->json(['message' => 'Category added successfully', 'category' => $category], 201);
    }
    public function editCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $category = Category::find($id);
        $category->update($validator->validated());

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }
    public function deleteCategory($id)
    {
        $category = Category::find($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    // CRUD for Comments

    public function showComments()
    {
        $comments = Comment::all();
        return response()->json(["comments" => $comments]);
    }
    public function addComment(Request $request)
    {


        // Data validation
        $validator = Validator::make($request->all(), [
            'author' => 'required|string',
            'comment' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create a new comment
        $comment = Comment::create($validator->validated());

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }
    public function editComment(Request $request, $id)
    {


        // Data validation
        $validator = Validator::make($request->all(), [
            'author' => 'string',
            'comment' => 'string',
            'course_id' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find and update the comment
        $comment = Comment::findOrFail($id);
        $comment->update($validator->validated());

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }
    public function deleteComment($id)
    {


        // Delete the comment
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    // CRUD pour les instructeurs
    public function showInstructors(Request $request)
    {
        $instructors = Instructor::all();
        return response()->json(['instructors' => $instructors]);
    }
    public function addInstructor(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Création d'un nouvel instructeur
        $instructor = Instructor::create($validator->validated());

        return response()->json(['message' => 'Instructor added successfully', 'instructor' => $instructor], 201);
    }

    public function editInstructor(Request $request, $id)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'first_name' => 'string',
            'last_name' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Recherche et mise à jour de l'instructeur
        $instructor = Instructor::findOrFail($id);
        $instructor->update($validator->validated());

        return response()->json(['message' => 'Instructor updated successfully', 'instructor' => $instructor]);
    }
    public function deleteInstructor($id)
    {
        // Suppression d'un instructeur
        $instructor = Instructor::findOrFail($id);
        $instructor->delete();

        return response()->json(['message' => 'Instructor deleted successfully']);
    }

    // Affichage et filtrer de la liste des demandes de rejoindre les cours des étudiants
    public function listAndFilterStudentCourseRequests(Request $request)
    {
        // Si le statut est fourni, filtrez les demandes en conséquence
        if ($request->has('status')) {
            // Valider le paramètre status
            $validator = Validator::make($request->all(), [
                'status' => 'string|in:pending,accepted,rejected',
            ]);

            // Si la validation échoue, renvoyer les erreurs
            if ($validator->fails()) {
                return response()->json(['error' => 'Bad request'], 400);
            }

            // Filtrer les demandes en fonction du statut
            $requests = StudentCourse::where('status', $request->status)->get();
        } else {
            // Récupérer les demandes en attente si aucun statut n'est fourni
            $requests = StudentCourse::where('status', 'pending')->get();
        }

        return response()->json(['requests' => $requests]);
    }
    // Accepter une demande de rejoindre un cours
    public function acceptCourseRequest($id)
    {
        // Trouver la demande de cours spécifiée
        $request = StudentCourse::find($id);

        // Mettre à jour le statut de la demande à "accepté"
        $request->status = 'accepted';
        $request->save();

        return response()->json(['message' => 'Course request accepted successfully']);
    }
    // Rejeter une demande de rejoindre un cours
    public function rejectCourseRequest($id)
    {
        // Trouver la demande de cours spécifiée
        $request = StudentCourse::find($id);

        // Mettre à jour le statut de la demande à "rejeté"
        $request->status = 'rejected';
        $request->save();

        return response()->json(['message' => 'Course request rejected successfully']);
    }

    //Gestion des Feedbacks

    public function showFeedbacks()
    {
        $feedbacks = Feedback::all();
        return response()->json(['feedbacks' => $feedbacks]);
    }

    // Supprimer Feedback
    public function deleteFeedback($id)
    {
        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json(['error' => 'Feedback not found'], 404);
        }

        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted successfully']);
    }

    //Gestion des Utilisateurs

    public function showUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }
}
