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
            $table->string('phone')->nullable()->after('email');
            $table->enum('global_role', ['super_admin', 'school_admin', 'teacher', 'financier', 'student', 'parent'])->default('teacher')->after('password');
            $table->boolean('is_2fa_enabled')->default(true)->after('global_role');
            $table->string('google2fa_secret')->nullable()->after('is_2fa_enabled');
            $table->boolean('is_active')->default(true)->after('google2fa_secret');
            $table->json('profile_data')->nullable()->after('is_active'); // Données supplémentaires du profil
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'global_role',
                'is_2fa_enabled',
                'google2fa_secret',
                'is_active',
                'profile_data'
            ]);
        });
    }
};
