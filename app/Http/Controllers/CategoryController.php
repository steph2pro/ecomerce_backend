<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Récupérer toutes les catégories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'intitule' => 'required|string|max:255|unique:categories,intitule',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'intitule' => $request->intitule,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    // Afficher une seule catégorie
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return response()->json($category);
    }

    // Mettre à jour une catégorie
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $request->validate([
            'intitule' => 'required|string|max:255|unique:categories,intitule,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'intitule' => $request->intitule,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'category' => $category
        ]);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }

    // Récupérer tous les articles d'une catégorie
    public function getArticles($id)
    {
        $category = Category::with('articles')->find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return response()->json($category->articles);
    }
}
