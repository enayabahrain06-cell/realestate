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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'blood_type')) {
                $table->string('blood_type')->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('blood_type');
            }
            if (!Schema::hasColumn('users', 'addresses')) {
                $table->json('addresses')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'social_links')) {
                $table->json('social_links')->nullable()->after('addresses');
            }
            if (!Schema::hasColumn('users', 'media_gallery')) {
                $table->json('media_gallery')->nullable()->after('social_links');
            }
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mobile', 'gender', 'birthdate', 'blood_type', 
                'nationality', 'addresses', 'social_links', 
                'media_gallery', 'full_name'
            ]);
        });
    }
};

