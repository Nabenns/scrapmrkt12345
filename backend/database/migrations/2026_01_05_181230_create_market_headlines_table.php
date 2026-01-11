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
        Schema::create('market_headlines', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->string('category', 100)->nullable();
            $table->timestamp('published_at')->useCurrent();
            $table->string('source', 50)->default('MRKT');
            $table->string('hash', 64)->unique();
            $table->timestamps();

            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_headlines');
    }
};
