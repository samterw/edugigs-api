<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gig_slots', function (Blueprint $table) {
            $table->softDeletes(); // This adds the 'deleted_at' column
        });
    }

    public function down()
    {
        Schema::table('gig_slots', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
