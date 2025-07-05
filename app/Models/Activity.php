<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'specific_objective_id',
        'responsible_id',
        'goal_id',
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
            'specific_objective_id' => 'integer',
            'responsible_id' => 'integer',
            'goal_id' => 'integer',
        ];
    }

    public function specificObjective(): BelongsTo
    {
        return $this->belongsTo(SpecificObjective::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Responsible::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function activityCalendars(): HasMany
    {
        return $this->hasMany(ActivityCalendar::class);
    }

    public function activityFiles(): HasMany
    {
        return $this->hasMany(ActivityFile::class);
    }

    public function beneficiaryRegistries(): HasMany
    {
        return $this->hasMany(BeneficiaryRegistry::class);
    }

    public function plannedMetrics(): HasMany
    {
        return $this->hasMany(PlannedMetric::class);
    }
}
