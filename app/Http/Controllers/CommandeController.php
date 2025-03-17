<?php
namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    // Récupérer toutes les commandes avec les articles associés
    public function index()
    {
        $commandes = Commande::with('user', 'commandeArticles.article')->get();
        return response()->json($commandes);
    }

    // Créer une commande avec ses articles
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'articles' => 'required|array',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction(); // Démarre une transaction

        try {
            // Calculer le montant total
            $montantTotal = collect($request->articles)->sum(function ($article) {
                return $article['quantite'] * $article['prix'];
            });

            // Créer la commande
            $commande = Commande::create([
                'user_id' => $request->user_id,
                'montantTotal' => $montantTotal,
                'statut' => 'En cours',
            ]);

            // Ajouter les articles à la commande
            foreach ($request->articles as $article) {
                CommandeArticle::create([
                    'commande_id' => $commande->id,
                    'article_id' => $article['article_id'],
                    'quantite' => $article['quantite'],
                    'prix' => $article['prix'],
                    'date_commande' => now(),
                ]);
            }

            DB::commit(); // Valider la transaction

            return response()->json([
                'message' => 'Commande créée avec succès',
                'commande' => $commande->load('commandeArticles.article')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Annuler en cas d'erreur
            return response()->json(['message' => 'Erreur lors de la création de la commande', 'error' => $e->getMessage()], 500);
        }
    }

    // Afficher une commande spécifique
    public function show($id)
    {
        $commande = Commande::with('user', 'commandeArticles.article')->find($id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }
        return response()->json($commande);
    }

    // Mettre à jour une commande
    public function update(Request $request, $id)
    {
        $commande = Commande::find($id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $request->validate([
            'statut' => 'sometimes|string',
        ]);

        $commande->update($request->only(['statut']));

        return response()->json([
            'message' => 'Commande mise à jour avec succès',
            'commande' => $commande
        ]);
    }

    // Supprimer une commande avec ses articles
    public function destroy($id)
    {
        $commande = Commande::find($id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        DB::beginTransaction();
        try {
            // Supprimer les articles associés à la commande
            $commande->commandeArticles()->delete();
            // Supprimer la commande
            $commande->delete();
            DB::commit();

            return response()->json(['message' => 'Commande supprimée avec succès']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la suppression', 'error' => $e->getMessage()], 500);
        }
    }
}
