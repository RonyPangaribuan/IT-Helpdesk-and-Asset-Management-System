<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Ticket|null $ticket */
        $ticket = $this->route('ticket');
        /** @var TicketComment|null $comment */
        $comment = $this->route('comment');

        return $ticket instanceof Ticket
            && $comment instanceof TicketComment
            && $comment->ticket_id === $ticket->id
            && ($this->user()?->can('update', $comment) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
