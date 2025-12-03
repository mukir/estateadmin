<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->boolean('reminder_opt_out')->default(false)->after('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reference');
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 50); // call, sms, email, visit, auto-email
            $table->string('status_tag', 50)->nullable(); // promise_to_pay, dispute, reminder
            $table->date('next_action_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('last_reminder_sent_at');
        });
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn('reminder_opt_out');
        });
    }
};
