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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('asset_id')
                ->nullable()
                ->after('ticket_category_id')
                ->constrained('assets')
                ->restrictOnDelete();

            $table->index('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropIndex(['asset_id']);
            $table->dropColumn('asset_id');
        });
    }
};
