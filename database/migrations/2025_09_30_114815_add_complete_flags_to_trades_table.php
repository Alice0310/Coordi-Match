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
        Schema::table('trades', function (Blueprint $table) {
            $table->boolean('completed_by_user')->default(false);   // ユーザー終了申請
            $table->boolean('completed_by_stylist')->default(false); // スタイリスト確認
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['completed_by_user', 'completed_by_stylist']);
        });
    }
};
