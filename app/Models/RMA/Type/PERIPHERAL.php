<?php

namespace App\Models\RMA\Type;

use App\Models\RMA\Type\Validators\AllowAnyIdentifierValidator;
use App\Models\RMA\Type\Validators\ValidatesIdentifiers;

class PERIPHERAL extends BaseIdentifiableEnum
{
    public const CABLE = "cable";
    public const SCREWS = "screws";

    /**
     * @inheritDoc
     */
    protected function getValidator(): ValidatesIdentifiers
    {
        return new AllowAnyIdentifierValidator();
    }
}
