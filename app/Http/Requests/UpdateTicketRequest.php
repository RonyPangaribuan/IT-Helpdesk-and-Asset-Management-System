<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\TicketPriority;
use App\Models\Asset;
use App\Models\Ticket;
use Closure;
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
        /** @var Ticket $ticket */
        $ticket = $this->route('ticket');
        $categoryRule = [
            'required',
            Rule::exists('ticket_categories', 'id')->where('is_active', true)->whereNull('deleted_at'),
        ];
        $assetRule = ['nullable', $this->assetRule($ticket)];

        if ($this->user()?->isAdmin()) {
            return [
                'ticket_category_id' => $categoryRule,
                'priority' => ['required', Rule::in(TicketPriority::values())],
                'asset_id' => $assetRule,
            ];
        }

        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'ticket_category_id' => $categoryRule,
            'priority' => ['required', Rule::in(TicketPriority::values())],
            'location' => ['required', 'string', 'max:150'],
            'asset_id' => $assetRule,
        ];
    }

    private function assetRule(Ticket $ticket): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($ticket): void {
            if ($value === null) {
                return;
            }

            $asset = Asset::withTrashed()->find($value);

            if (! $asset instanceof Asset) {
                $fail('The selected asset is invalid.');

                return;
            }

            if ((int) $value === $ticket->asset_id) {
                return;
            }

            if ($asset->trashed() || ! $asset->is_active || $asset->condition === AssetCondition::Retired) {
                $fail('The selected asset is invalid.');
            }
        };
    }
}
