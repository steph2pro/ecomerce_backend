<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    // Récupérer tous les utilisateurs
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Créer un nouvel utilisateur
    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'sexe' => 'required|string|in:homme,femme,autre',
        'adresse' => 'nullable|string',
        'telephone' => 'required|string|unique:users,telephone',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|string|in:admin,user',
    ]);

    // Création de l'utilisateur SANS l'image d'abord (on a besoin de l'ID)
    $user = User::create([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'sexe' => $request->sexe,
        'adresse' => $request->adresse,
        'telephone' => $request->telephone,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    // Gestion de l'image
    if ($request->hasFile('image')) {
        $image = $request->file('image');

        // Définition du répertoire personnalisé
        $directory = base_path('../profil_users');

        // Vérifier si le dossier existe, sinon le créer
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }

        // Générer un nom unique basé sur l'ID et la date actuelle
        $imageName = 'User_' . $user->id . '_' . date('Ymd_His') . '.' . $image->getClientOriginalExtension();

        // Déplacer l’image dans le répertoire
        $image->move($directory, $imageName);

        // Stocker le chemin relatif dans la base de données
        $user->update(['image' => 'profil_users/' . $imageName]);
    }

    return response()->json([
        'message' => 'Utilisateur créé avec succès',
        'user' => $user
    ], 201);
}
    // Afficher un utilisateur spécifique
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($user);
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'sexe' => 'required|string|in:homme,femme,autre',
            'adresse' => 'nullable|string',
            'telephone' => 'required|string|unique:users,telephone,' . $id, // Assure-toi que ce n'est pas unique par rapport à cet ID
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'email' => 'required|email|unique:users,email,' . $id, // Assure-toi que ce n'est pas unique par rapport à cet ID
            'password' => 'nullable|string|min:6', // Le mot de passe est optionnel à la mise à jour
            'role' => 'required|string|in:admin,user',
        ]);

        // Trouver l'utilisateur par ID
        $user = User::findOrFail($id);

        // Mise à jour des données de l'utilisateur
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->sexe = $request->sexe;
        $user->adresse = $request->adresse;
        $user->telephone = $request->telephone;
        $user->email = $request->email;
        $user->role = $request->role;

        // Si un mot de passe est fourni, on le met à jour
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image s'il y en a une
            $this->deleteOldImage($user->image);

            // Gestion de la nouvelle image
            $image = $request->file('image');
            $directory = base_path('../profil_users');

            // Vérifier si le dossier existe, sinon le créer
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            // Générer un nouveau nom d'image unique
            $imageName = 'User_' . $user->id . '_' . date('Ymd_His') . '.' . $image->getClientOriginalExtension();

            // Déplacer l’image dans le répertoire
            $image->move($directory, $imageName);

            // Mettre à jour le chemin de l'image dans la base de données
            $user->image = 'profil_users/' . $imageName;
        }

        // Sauvegarder l'utilisateur
        $user->save();

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user
        ], 200);
    }

    // Méthode pour supprimer l'ancienne image si elle existe
    protected function deleteOldImage($imagePath)
    {
        $fullImagePath = base_path('../') . $imagePath;

        if (File::exists($fullImagePath)) {
            File::delete($fullImagePath);
        }
    }
    // Supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    // Récupérer les articles d'un utilisateur
    public function getArticles($id)
    {
        $user = User::with('articles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($user->articles);
    }

    // Récupérer les commandes d'un utilisateur
    public function getCommandes($id)
    {
        $user = User::with('commande')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        return response()->json($user->commande);
    }
}
