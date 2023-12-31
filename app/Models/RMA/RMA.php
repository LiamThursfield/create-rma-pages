<?php

namespace App\Models\RMA;

use App\Http\Requests\CreateRMARequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RMA extends Model
{
    use HasFactory;

    protected $table = 'rmas';

    protected $guarded = ['id'];

    /**
     * @param CreateRMARequest $request
     * @return RMA
     */
    public static function createFromRequest(CreateRMARequest $request): RMA
    {
        return DB::transaction(function () use ($request) {
            $rma = RMA::create([
                'user_id' => $request->user()->id
            ]);

            foreach ($request->getItems() as $data) {
                RMAItem::createFromData($rma, $data);
            }

            return $rma;
        });
    }

    /**
     * Get the user that created this RMA
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items that belong to this RMA
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(RMAItem::class, 'rma_id');
    }
}
