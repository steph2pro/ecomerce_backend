<?php
// Commande.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    // Les champs qui peuvent être assignés en masse
    protected $fillable = [
        'user_id',
        'montantTotal',
        'statut',
    ];

    /**
     * Définir la relation avec le modèle User (belongsTo).
     * Chaque commande appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    /**
     * Définir la relation avec la table 'commande_articles' (hasMany).
     * Chaque commande peut avoir plusieurs articles associés via 'commande_articles'.
     */
    public function commandeArticles()
    {
        return $this->hasMany(CommandeArticle::class);
    }
}
