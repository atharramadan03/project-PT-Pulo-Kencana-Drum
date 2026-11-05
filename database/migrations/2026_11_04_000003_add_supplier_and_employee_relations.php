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
        Schema::table('warehouse_finance_logs', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('drum_batch_id')->constrained('suppliers')->nullOnDelete();
        });

        Schema::table('operational_expense_logs', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('user_id')->constrained('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_expense_logs', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });

        Schema::table('warehouse_finance_logs', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
