<?php

namespace App\Models\RMA\Type\Validators;

use App\Models\RMA\Type\BaseIdentifiableEnum;
use App\Models\RMA\Type\INVERTER;

class InverterIdentifierValidator implements ValidatesIdentifiers
{
    /**
     * @param INVERTER $type
     * @inheritDoc
     */
    public function validate(BaseIdentifiableEnum $type, string $identifier): string|array|null
    {
        $errors = [];

        //the identifier must be 10 characters long
        if (strlen($identifier) !== 10) {
            $errors[] = 'The identifier must be 10 characters long';
        }

        $types_hybrid = [
            INVERTER::_5_KW_HYBRID,
        ];

        $types_coupled = [
            INVERTER::_3_KW_AC_COUPLED,
            INVERTER::_3_6_KW_AC_COUPLED
        ];

        //if the type is a hybrid inverter, the identifier must start with characters 'SA' or 'SD'
        if (in_array($type->value, $types_hybrid)) {
            if (!in_array(substr($identifier, 0, 2), ['SA', 'SD'])) {
                $errors[] = 'The identifier must start with characters \'SA\' or \'SD\'';
            }
        }
        //if the type is an AC coupled inverter, the identifier must start with characters 'CE'
        else if (in_array($type->value, $types_coupled)) {
            if (!str_starts_with($identifier, 'CE')) {
                $errors[] = 'The identifier must start with characters \'CE\'';
            }
        }

        //the 7th character in the identifier must be the letter 'G'
        if (substr($identifier, 6, 1) !== 'G') {
            $errors[] = 'The 7th character in the identifier must be the letter \'G\'';
        }

        //the rest of the identifier must be made up of numbers
        if (!is_numeric(substr($identifier, 2, 4))) {
            $errors[] = 'The identifier\'s 3rd to 6th characters must made up of numbers';
        }

        if (!is_numeric(substr($identifier, 7))) {
            $errors[] = 'The identifier must be made up of numbers from the 8th character onwards';
        }

        //the identifier is case-sensitive

        //CE2125G001 is VALID
        //SE2125H00B is NOT VALID

        return count($errors) ? $errors : null;
    }
}
