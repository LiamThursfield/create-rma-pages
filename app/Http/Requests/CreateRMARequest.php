<?php

namespace App\Http\Requests;

use App\Models\RMA\RMAItemData;
use App\Models\RMA\Type\RMA_TYPE;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use BenSampo\Enum\Rules\EnumValue;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * @property-read array $items
 */
class CreateRMARequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'items' => ['required', 'array'],
            'items.*.type' => ['required', new EnumValue(RMA_TYPE::class)],
            'items.*.value' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    try {
                        $rma_type = RMA_TYPE::fromValue($this[str_replace('value', 'type', $attribute)]);

                        $allowed_values = $rma_type->getAssociatedInstanceMembers()->toArray();
                        if (!in_array($value, $allowed_values)) {
                            $fail("The {$attribute} must be one of: " . Arr::join($allowed_values, ', ') . ".");
                        }
                    } catch (InvalidEnumMemberException) {
                        $fail("The {$attribute} is invalid.");
                    }
                },
            ],
            'items.*.identifier' => ['required', 'string'],
            'items.*.reason' => ['required', 'string'],
        ];
    }

    /**
     * @return RMAItemData[]
     */
    public function getItems(): array
    {
        return array_map(fn($data) => new RMAItemData($data), $this->items);
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function checkIdentifierValidation(): void
    {
        $messages = [];

        foreach ($this->getItems() as $index => $item) {
            $message = RMA_TYPE::fromValue($item->type)->validate($item->value, $item->identifier);

            if ($message !== null) {
                $messages["items.{$index}.identifier"] = $message;
            }
        }

        if (sizeof($messages) > 0) {
            throw ValidationException::withMessages($messages);
        }
    }
}
