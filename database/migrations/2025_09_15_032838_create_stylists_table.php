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
        Schema::create('stylists', function (Blueprint $table) {
            $table->id(); // id BIGINT AUTO_INCREMENT
            $table->unsignedBigInteger('user_id')->index(); // ユーザー紐付け
            $table->string('overview', 40);                // 概要
            $table->text('appeal')->nullable();            // アピール文
            $table->string('twitter')->nullable();         // Twitter
            $table->string('instagram')->nullable();       // Instagram
            $table->integer('price')->nullable();          // 価格
            $table->json('photos')->nullable();            // 写真 (JSON配列)
            $table->json('genres')->nullable();            // 得意ジャンル (JSON配列)
            $table->enum('status', ['draft', 'published'])->default('draft'); // 状態
            $table->timestamps();

            // 外部キー制約 (必要なら)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stylists');
    }
};
