<?php
// CommandeArticle.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeArticle extends Model
{
    use HasFactory;

    // Les champs qui peuvent être assignés en masse
    protected $fillable = [
        'commande_id',
        'article_id',
        'quantite',
        'prix',
        'date_commande',
    ];

    /**
     * Définir la relation avec le modèle Commande (belongsTo).
     * Chaque ligne de commande appartient à une commande.
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class,'commande_id');
    }

    /**
     * Définir la relation avec le modèle Article (belongsTo).
     * Chaque ligne de commande appartient à un article.
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
