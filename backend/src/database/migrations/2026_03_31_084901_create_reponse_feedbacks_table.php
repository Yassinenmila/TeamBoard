<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reponse_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('feedback_id')->containedIn('feedbacks')->onDelete('cascade');
            $table->ForeignId('user_id')->containedIn('users')->onDelete('cascade');
            $table->text('contenu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reponse_feedbacks');
    }
};
