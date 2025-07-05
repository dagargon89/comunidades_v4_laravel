<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedMetric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'unit',
        'year',
        'month',
        'is_product',
        'is_population',
        'population_target_value',
        'population_real_value',
        'product_target_value',
        'product_real_value',
        'data_collector_id',
        'activity_id',
        'nullable_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'population_target_value' => 'decimal:2',
            'population_real_value' => 'decimal:2',
            'product_target_value' => 'decimal:2',
            'product_real_value' => 'decimal:2',
            'data_collector_id' => 'integer',
            'activity_id' => 'integer',
            'nullable_id' => 'integer',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function $this->belongsTo(DataCollector::class)able(): BelongsTo
    {
        return $this->belongsTo(DataCollector::class);
    }

    public function dataCollector(): BelongsTo
    {
        return $this->belongsTo(DataCollectors,::class);
    }
}
