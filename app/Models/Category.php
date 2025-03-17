<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Les attributs qui peuvent être assignés en masse
    protected $fillable = [
        'intitule',
        'description',
    ];

    // Si tu utilises les timestamps, pas besoin de les mentionner explicitement
    // Laravel les gère automatiquement (created_at, updated_at)
    // Category.php
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

}
