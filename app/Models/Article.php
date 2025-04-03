<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Définir les colonnes qui peuvent être assignées en masse
    protected $fillable = [
        'intitule',
        'description',
        'quantite',
        'prix_unitaire_achat',
        'prix_unitaire_de_vente',
        'type_operation',
        'categorie_id',
        'user_id',
        'images',
    ];

    // Si tu veux protéger certaines colonnes (par exemple 'id')
    // protected $guarded = ['id'];

    // Si tu utilises des dates personnalisées
    protected $dates = ['created_at', 'updated_at'];

    // Relation avec le modèle Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Définir la relation avec la table 'commande_articles' (hasMany).
     * Un article peut être présent dans plusieurs lignes de commandes via 'commande_articles'.
     */
    public function commandeArticles()
    {
        return $this->hasMany(CommandeArticle::class);
    }
}
