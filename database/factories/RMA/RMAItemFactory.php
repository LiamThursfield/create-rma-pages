<?php

namespace Database\Factories\RMA;

use App\Models\RMA\RMA;
use App\Models\RMA\Type\BATTERY;
use App\Models\RMA\Type\INVERTER;
use App\Models\RMA\Type\RMA_TYPE;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RMA\RMAItem>
 */
class RMAItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return array_merge(['rma_id' => RMA::factory()],
            static::makeData($this->faker));
    }

    /**
     * @param Generator $faker
     * @return array
     */
    public static function makeData(Generator $faker): array
    {
        $type = RMA_TYPE::getRandomInstance();
        $value = call_user_func([$type->getAssociatedEnumClass(), 'getRandomValue']);

        return [
            'type' => $type->value,
            'value' => $value,
            'identifier' => strtoupper($faker->bothify(static::generateIdentifier($type->value, $value))),
            'reason' => $faker->sentence
        ];
    }

    public static function generateIdentifier(string $type, $value): string
    {
        if ($type === RMA_TYPE::BATTERY) {
            return self::generateBatteryIdentifier($value);
        } elseif ($type === RMA_TYPE::INVERTER) {
            return self::generateInverterIdentifier($value);
        }

        // Any
        return '??####?###';
    }

    public static function generateBatteryIdentifier($value): string
    {
        $identifier = 'BE';

        $battery_size_map = [
            BATTERY::_2_6_KWH => 26,
            BATTERY::_5_2_KWH => 52,
            BATTERY::_9_2_KWH => 92,
        ];
        $identifier .= Arr::get($battery_size_map, $value);

        return $identifier . '########';
    }

    public static function generateInverterIdentifier($value): string
    {
        $identifier = '';

        $types_hybrid = [
            INVERTER::_5_KW_HYBRID,
        ];

        if (in_array($value, $types_hybrid)) {
            $identifier .= 'SA';
        } else {
            $identifier .= 'CE';
        }

        return $identifier . '####G###';
    }
}
