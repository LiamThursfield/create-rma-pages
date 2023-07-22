<?php

namespace App\Models\RMA\Type\Validators;

use App\Models\RMA\Type\BaseIdentifiableEnum;
use App\Models\RMA\Type\BATTERY;
use Illuminate\Support\Arr;

class BatteryIdentifierValidator implements ValidatesIdentifiers
{
    /**
     * @param BATTERY $type
     * @inheritDoc
     */
    public function validate(BaseIdentifiableEnum $type, string $identifier): string|array|null
    {
        $errors = [];

        if (strlen($identifier) !== 12) {
            $errors[] = 'The identifier must be 12 characters long';
        }

        if (!in_array(substr($identifier, 0, 2), ['BE', 'BB', 'BG'])) {
            $errors[] = 'The identifier must start with characters \'BE\', \'BB\' or \'BG\'';
        }

        $battery_size_map = [
            BATTERY::_2_6_KWH => 26,
            BATTERY::_5_2_KWH => 52,
            BATTERY::_9_2_KWH => 92,
        ];
        $battery_size = Arr::get($battery_size_map, $type->value);
        if ((int)substr($identifier, 2, 2) !== $battery_size) {
            $errors[] = 'The identifier\'s 3rd and 4th characters must be the battery size times 10 (e.g. 9.5kWh = 95)';
        }

        if (!is_numeric(substr($identifier, 4))) {
            $errors[] = 'The identifier must be made up of numbers from the 5th character onwards';
        }

        return count($errors) ? $errors : null;
    }
}
