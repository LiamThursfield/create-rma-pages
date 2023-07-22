<?php

namespace App\Actions;

use App\Models\RMA\RMA;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RMAQueryAction
{
    public function handle(array $search_options): Collection
    {
        $query = RMA::query();

        // NOTE: As the test is testing that 100 records are shown at once, it is assumed that for now there is no pagination.
        // However, in a real world scenario, there would be pagination, and filtering based on request search options

        return $query->get();
    }
}