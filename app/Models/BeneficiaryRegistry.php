<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryRegistry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name',
        'mother_last_name',
        'first_names',
        'birth_year',
        'gender',
        'phone',
        'signature',
        'address_backup',
        'activity_id',
        'location_id',
        'nullable_id',
        'identifier',
        'activity_date',
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
            'activity_id' => 'integer',
            'location_id' => 'integer',
            'nullable_id' => 'integer',
            'activity_date' => 'date',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function dataCollector(): BelongsTo
    {
        return $this->belongsTo(DataCollector::class);
    }
}
