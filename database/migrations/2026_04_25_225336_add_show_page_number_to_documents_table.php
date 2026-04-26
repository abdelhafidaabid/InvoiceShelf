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
            $table->boolean('show_page_number')->default(true)->after('show_signature');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('show_page_number')->default(true)->after('show_signature');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('show_page_number')->default(true)->after('show_signature');
        });

        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->boolean('show_page_number')->default(true)->after('show_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('show_page_number');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('show_page_number');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('show_page_number');
        });

        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->dropColumn('show_page_number');
        });
    }
};
