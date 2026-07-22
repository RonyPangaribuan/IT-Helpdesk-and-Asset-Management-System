<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\TicketPriority;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Ticket::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'asset_id' => $this->input('asset_id') === '' ? null : $this->input('asset_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'ticket_category_id' => [
                'required',
                Rule::exists('ticket_categories', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
            'priority' => ['required', Rule::in(TicketPriority::values())],
            'location' => ['required', 'string', 'max:150'],
            'asset_id' => [
                'nullable',
                Rule::exists('assets', 'id')
                    ->where('is_active', true)
                    ->where('condition', '!=', AssetCondition::Retired->value)
                    ->whereNull('deleted_at'),
            ],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf',
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],
        ];
    }
}
