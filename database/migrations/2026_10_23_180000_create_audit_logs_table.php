<?php

declare(strict_types=1);

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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('service', 100);
            $table->string('method', 10);
            $table->string('path', 255);
            $table->json('request_body')->nullable();
            $table->unsignedSmallInteger('response_status');
            $table->longText('response_body')->nullable();
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->useCurrent();

            $table->index('service');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
