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
        Schema::create('market_sentiments', function (Blueprint $table) {
            $table->id();
            $table->integer('value');
            $table->string('label');
            $table->string('risk_regime');
            $table->text('reasoning');
            $table->timestamps();
        });

        Schema::create('economic_summaries', function (Blueprint $table) {
            $table->id();
            $table->text('content'); // Stores the 'overall_summary' JSON or text
            $table->timestamps();
        });

        Schema::create('trump_events', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('type');
            $table->text('details');
            $table->string('location')->nullable();
            $table->string('hash')->unique(); // For deduplication
            $table->timestamps();
        });

        Schema::create('trump_volatility', function (Blueprint $table) {
            $table->id();
            $table->integer('score');
            $table->text('explanation');
            $table->timestamps();
        });

        Schema::create('etf_summaries', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etf_summaries');
        Schema::dropIfExists('trump_volatility');
        Schema::dropIfExists('trump_events');
        Schema::dropIfExists('economic_summaries');
        Schema::dropIfExists('market_sentiments');
    }
};
