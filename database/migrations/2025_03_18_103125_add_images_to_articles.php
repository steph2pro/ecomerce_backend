<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagesToArticles extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            // Ajouter une colonne 'images' de type JSON pour stocker une liste d'images
            $table->json('images')->nullable();  // nullable() pour autoriser les enregistrements sans images
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            // Supprimer la colonne 'images'
            $table->dropColumn('images');
        });
    }
}
