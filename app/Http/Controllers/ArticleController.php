<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'intitule' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'quantite' => 'required|integer',
    //         'prix_unitaire_achat' => 'required|numeric',
    //         'prix_unitaire_de_vente' => 'required|numeric',
    //         'type_operation' => 'required|string',
    //         'categorie_id' => 'required|integer|exists:categories,id',
    //         'user_id' => 'required|integer|exists:users,id',
    //         // 'user_id' => 'required|exists:users,id',
    //     ]);

    //     $article = Article::create($validated);
    //     return response()->json($article, 201);
    // }
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'description' => 'required|string',
            'quantite' => 'required|integer',
            'prix_unitaire_achat' => 'required|numeric',
            'prix_unitaire_de_vente' => 'required|numeric',
            'type_operation' => 'required|string',
            'categorie_id' => 'required|integer|exists:categories,id',
            'user_id' => 'required|integer|exists:users,id',
            'images' => 'nullable|array', // Validation pour accepter un tableau d'images
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048', // Validation pour chaque image
        ]);

        // Création de l'article sans les images
        $article = Article::create([
            'intitule' => $request->intitule,
            'description' => $request->description,
            'quantite' => $request->quantite,
            'prix_unitaire_achat' => $request->prix_unitaire_achat,
            'prix_unitaire_de_vente' => $request->prix_unitaire_de_vente,
            'type_operation' => $request->type_operation,
            'categorie_id' => $request->categorie_id,
            'user_id' => $request->user_id,
        ]);

        // Gestion des images
        $imagePaths = []; // Tableau pour stocker les chemins des images

        if ($request->hasFile('images')) {
            $images = $request->file('images');

            // Définir le répertoire où les images seront stockées
            $directory = base_path('../article_images');

            // Vérifier si le répertoire existe, sinon le créer
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            // Parcourir chaque image et la traiter
            foreach ($images as $image) {
                // Générer un nom unique pour chaque image
                $imageName = 'Article_' . $article->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Déplacer l’image vers le répertoire de stockage
                $image->move($directory, $imageName);

                // Ajouter le chemin relatif de l'image dans le tableau
                $imagePaths[] = 'article_images/' . $imageName;
            }

            // Mettre à jour l'article avec les images stockées
            $article->update(['images' => json_encode($imagePaths)]); // Stocker les chemins des images sous forme de JSON
        }

        return response()->json([
            'message' => 'Article créé avec succès',
            'article' => $article
        ], 201);
    }
    public function update(Request $request, $id)
    {
        return $request;
        // Validation des données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'description' => 'required|string',
            'quantite' => 'required|integer',
            'prix_unitaire_achat' => 'required|numeric',
            'prix_unitaire_de_vente' => 'required|numeric',
            'type_operation' => 'required|string',
            'categorie_id' => 'required|integer|exists:categories,id',
            'user_id' => 'required|integer|exists:users,id',
            'images' => 'nullable|array', // Validation pour accepter un tableau d'images
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048', // Validation pour chaque image
        ]);

        // Récupérer l'article
        $article = Article::findOrFail($id);

        // Mettre à jour les champs de l'article
        $article->update([
            'intitule' => $request->intitule,
            'description' => $request->description,
            'quantite' => $request->quantite,
            'prix_unitaire_achat' => $request->prix_unitaire_achat,
            'prix_unitaire_de_vente' => $request->prix_unitaire_de_vente,
            'type_operation' => $request->type_operation,
            'categorie_id' => $request->categorie_id,
            'user_id' => $request->user_id,
        ]);

        // Gestion des images
        if ($request->hasFile('images')) {
            // Supprimer les anciennes images
            if ($article->images) {
                $oldImages = json_decode($article->images, true);
                foreach ($oldImages as $oldImage) {
                    $oldImagePath = base_path('../' . $oldImage);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
            }

            // Définir le répertoire de stockage
            $directory = base_path('../article_images');

            // Vérifier si le répertoire existe, sinon le créer
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            // Stocker les nouvelles images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = 'Article_' . $article->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($directory, $imageName);
                $imagePaths[] = 'article_images/' . $imageName;
            }

            // Mettre à jour l'article avec les nouvelles images
            $article->update(['images' => json_encode($imagePaths)]);
        }

        return response()->json([
            'message' => 'Article mis à jour avec succès',
            'article' => $article
        ], 200);
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
