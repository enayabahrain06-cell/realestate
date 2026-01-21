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
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guardian_user_id');
            $table->unsignedBigInteger('dependent_user_id');
            $table->string('relationship_type');
            $table->boolean('is_billing_contact')->default(false);
            $table->timestamps();

            $table->foreign('guardian_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('dependent_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index(['guardian_user_id', 'dependent_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relationships');
    }
};

