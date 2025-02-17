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
        Schema::create('draft_translations', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('content');


            $table->string('locale')->index();
            $table->unique(['draft_id','locale']);
            $table->foreignId('draft_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_translations');
    }
};
