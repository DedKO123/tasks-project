<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->enum('status', TaskStatus::getValues())->index();
            $table->enum('priority', TaskPriority::getValues())->index();
            $table->string('title');
            $table->text('description');
            $table->timestamp('completed_at')->nullable()->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'priority']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['created_at', 'completed_at', 'priority']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'priority', 'created_at', 'completed_at']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->fulltext(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
