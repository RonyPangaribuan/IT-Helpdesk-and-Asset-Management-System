<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->restrictOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('ticket_id');
            $table->index('changed_by');
            $table->index(['ticket_id', 'created_at', 'id']);
        });

        DB::table('tickets')
            ->select(['id', 'requester_id', 'status', 'created_at'])
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('ticket_status_histories')
                    ->whereColumn('ticket_status_histories.ticket_id', 'tickets.id');
            })
            ->orderBy('id')
            ->chunkById(100, function ($tickets): void {
                $rows = [];

                foreach ($tickets as $ticket) {
                    $rows[] = [
                        'ticket_id' => $ticket->id,
                        'changed_by' => $ticket->requester_id,
                        'old_status' => null,
                        'new_status' => $ticket->status,
                        'note' => 'Ticket created',
                        'created_at' => $ticket->created_at,
                    ];
                }

                if ($rows !== []) {
                    DB::table('ticket_status_histories')->insert($rows);
                }
            }, 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_histories');
    }
};
