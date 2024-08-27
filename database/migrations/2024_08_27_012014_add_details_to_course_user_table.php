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
        Schema::table('course_user', function (Blueprint $table) {
            $table->timestamp('enrolled_at')->nullable()->after('user_id');
            $table->string('status')->default('active')->after('enrolled_at');
            $table->decimal('price_paid', 8, 2)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn(['enrolled_at', 'status', 'price_paid']);
        });
    }
};
