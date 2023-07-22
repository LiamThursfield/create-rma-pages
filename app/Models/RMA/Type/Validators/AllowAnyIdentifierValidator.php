<?php

namespace App\Models\RMA\Type\Validators;

use App\Models\RMA\Type\BaseIdentifiableEnum;
use App\Models\RMA\Type\BATTERY;
use Illuminate\Support\Arr;

class AllowAnyIdentifierValidator implements ValidatesIdentifiers
{
    /**
     * @param BaseIdentifiableEnum $type
     * @inheritDoc
     */
    public function validate(BaseIdentifiableEnum $type, string $identifier): string|array|null
    {
        return null;
    }
}
