<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryActivityCalendar extends Model
{
    use HasFactory;

    protected $table = 'beneficiary_activity_calendar';

    protected $fillable = [
        'beneficiary_registry_id',
        'activity_id',
        'activity_calendar_id',
        'signature',
    ];

    public function beneficiaryRegistry()
    {
        return $this->belongsTo(BeneficiaryRegistry::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function activityCalendar()
    {
        return $this->belongsTo(ActivityCalendar::class);
    }
}
