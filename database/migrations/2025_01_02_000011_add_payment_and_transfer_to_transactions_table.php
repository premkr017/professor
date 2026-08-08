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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->default('cash')->after('transaction_date');
            $table->string('attachment')->nullable()->after('payment_method');
            $table->foreignId('to_account_id')->nullable()->after('category_id')
                ->constrained('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('to_account_id');
            $table->dropColumn(['payment_method', 'attachment']);
        });
    }
};

