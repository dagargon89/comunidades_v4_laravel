<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
        'street',
        'neighborhood',
        'ext_number',
        'int_number',
        'google_place_id',
        'polygon_id',
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
            'created_at' => 'timestamp',
            'polygon_id' => 'integer',
        ];
    }

    public function polygon(): BelongsTo
    {
        return $this->belongsTo(Polygon::class);
    }

    public function activityCalendars(): HasMany
    {
        return $this->hasMany(ActivityCalendar::class);
    }

    public function beneficiaryRegistries(): HasMany
    {
        return $this->hasMany(BeneficiaryRegistry::class);
    }
}
