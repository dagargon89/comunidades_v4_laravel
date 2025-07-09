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
        'activity_calendar_id',
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
            'activity_calendar_id' => 'integer',
        ];
    }



    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function dataCollector(): BelongsTo
    {
        return $this->belongsTo(DataCollector::class);
    }

    public function activityCalendar(): BelongsTo
    {
        return $this->belongsTo(ActivityCalendar::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public static function generarCurpFiltro($first_names, $last_name, $mother_last_name, $birth_year, $gender)
    {
        $iniciales = strtoupper(
            substr($last_name, 0, 1) .
            substr($mother_last_name, 0, 1) .
            substr($first_names, 0, 1) .
            (isset(explode(' ', $first_names)[1]) ? substr(explode(' ', $first_names)[1], 0, 1) : 'X')
        );
        $anio = $birth_year;
        $sexo = strtoupper(substr($gender, 0, 1));
        $internas = strtoupper(
            (isset($last_name[1]) ? $last_name[1] : 'X') .
            (isset($mother_last_name[1]) ? $mother_last_name[1] : 'X') .
            (isset($first_names[1]) ? $first_names[1] : 'X')
        );
        return $iniciales . $anio . $sexo . $internas;
    }
}
