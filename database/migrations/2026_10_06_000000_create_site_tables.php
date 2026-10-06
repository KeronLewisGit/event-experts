<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('reference')->nullable()->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('summary')->nullable();
            $table->json('data')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category')->index();
            $table->date('event_date')->nullable();
            $table->string('venue')->nullable();
            $table->string('summary');
            $table->text('body')->nullable();
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->json('credits')->nullable();
            $table->string('instagram_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('booked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('status')->default('booked');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('summary')->nullable();
            $table->text('requirements')->nullable();
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('booked_dates');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('enquiries');
    }
};
