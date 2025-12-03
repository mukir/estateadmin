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
            // Allow same email across different businesses
            $table->dropUnique('users_email_unique');

            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained('businesses')
                ->nullOnDelete();

            $table->boolean('is_business_owner')->default(false)->after('password');
            $table->string('status')->default('active')->after('is_business_owner');

            $table->unique(['business_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_business_id_email_unique');
            $table->dropColumn(['business_id', 'is_business_owner', 'status']);
            $table->unique('email');
        });
    }
};
