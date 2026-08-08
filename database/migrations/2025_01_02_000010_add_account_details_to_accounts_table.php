<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand accounts with bank details and additional account types.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('accounts', 'account_number')) {
                $table->string('account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('accounts', 'opening_balance')) {
                $table->decimal('opening_balance', 15, 2)->default(0)->after('balance');
            }
        });

        // Expand type enum to support all required account types.
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', [
                'cash', 'bank', 'savings', 'current', 'credit_card',
                'upi', 'e_wallet', 'investment', 'other',
            ])->default('bank')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum back to original set.
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('type', ['cash', 'bank', 'card', 'e_wallet', 'other'])->default('bank')->change();
        });

        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'bank_name')) {
                $table->dropColumn(['bank_name', 'account_number', 'opening_balance']);
            }
        });
    }
};

