<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Ticket|null $ticket */
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket && ($this->user()?->can('update', $ticket) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryRule = [
            'required',
            Rule::exists('ticket_categories', 'id')->where('is_active', true)->whereNull('deleted_at'),
        ];

        if ($this->user()?->isAdmin()) {
            return [
                'ticket_category_id' => $categoryRule,
                'priority' => ['required', Rule::in(TicketPriority::values())],
            ];
        }

        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'ticket_category_id' => $categoryRule,
            'priority' => ['required', Rule::in(TicketPriority::values())],
            'location' => ['required', 'string', 'max:150'],
        ];
    }
}
