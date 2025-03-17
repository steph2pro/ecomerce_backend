<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return response()->json(Article::with(['category', 'user'])->get(), 200);
    }

    public function show($id)
    {
        $article = Article::with(['category', 'user'])->find($id);
        return $article ? response()->json($article, 200) : response()->json(['message' => 'Article non trouvé'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intitule' => 'required|string|max:255',
            'description' => 'required|string',
            'quantite' => 'required|integer',
            'prix_unitaire_achat' => 'required|numeric',
            'prix_unitaire_de_vente' => 'required|numeric',
            'type_operation' => 'required|string',
            'categorie_id' => 'required|integer|exists:categories,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $article = Article::create($validated);
        return response()->json($article, 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $validated = $request->validate([
            'intitule' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantite' => 'nullable|integer',
            'prix_unitaire_achat' => 'nullable|numeric',
            'prix_unitaire_de_vente' => 'nullable|numeric',
            'type_operation' => 'nullable|string',
            'categorie_id' => 'nullable|integer|exists:categories,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $article->update($validated);
        return response()->json($article, 200);
    }

    public function destroy($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $article->delete();
        return response()->json(['message' => 'Article supprimé'], 200);
    }
}
