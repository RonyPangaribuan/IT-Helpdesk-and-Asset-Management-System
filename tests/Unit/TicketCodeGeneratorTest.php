<?php

namespace Tests\Unit;

use App\Models\Ticket;
use PHPUnit\Framework\TestCase;

class TicketCodeGeneratorTest extends TestCase
{
    public function test_pending_code_is_not_empty_and_unique(): void
    {
        $first = Ticket::pendingCode();
        $second = Ticket::pendingCode();

        $this->assertNotEmpty($first);
        $this->assertNotEmpty($second);
        $this->assertNotSame($first, $second);
        $this->assertStringStartsWith('PENDING-', $first);
    }

    public function test_final_code_uses_year_and_six_digit_padding(): void
    {
        $this->assertSame('TCK-2026-000001', Ticket::codeFromId(1, 2026));
        $this->assertSame('TCK-2027-123456', Ticket::codeFromId(123456, 2027));
    }

    public function test_codes_from_different_ids_are_different(): void
    {
        $this->assertNotSame(
            Ticket::codeFromId(1, 2026),
            Ticket::codeFromId(2, 2026),
        );
    }
}
