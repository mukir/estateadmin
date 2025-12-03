<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('enforce_mfa')->default(false)->after('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mfa_enabled')->default(false)->after('status');
            $table->text('mfa_secret')->nullable()->after('mfa_enabled');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('enforce_mfa');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mfa_enabled');
            $table->dropColumn('mfa_secret');
        });
    }
};
