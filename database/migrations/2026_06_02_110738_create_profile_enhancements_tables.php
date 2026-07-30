<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Append the 5 requested social media columns directly to your users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('social_whatsapp')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_telegram')->nullable();
            $table->string('social_email')->nullable();
        });

        // 2. Create the standalone seller portfolios table for promotional flyers/work
        Schema::create('seller_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->timestamps();
        });

        // 3. Create the lookup table for the gamification badges
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description');
            $table->string('icon_class'); // Holds Tailwind styling parameters
            $table->timestamps();
        });

        // 4. Create the pivot table linking earned badges dynamically to users
        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('seller_portfolios');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'social_whatsapp', 
                'social_instagram', 
                'social_facebook', 
                'social_telegram', 
                'social_email'
            ]);
        });
    }
};