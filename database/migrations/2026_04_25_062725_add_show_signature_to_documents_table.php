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
        Schema::table('estimates', function (Blueprint $table) {
            $table->boolean('show_signature')->default(true)->after('template_name');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('show_signature')->default(true)->after('template_name');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('show_signature')->default(true)->after('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('show_signature');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('show_signature');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('show_signature');
        });
    }
};
