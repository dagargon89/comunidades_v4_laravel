<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySummary extends Model
{
    protected $table = 'vw_activity_summary';
    public $timestamps = false;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'activity_id',
        'activity_name',
        'specific_objective_id',
        'objective_description',
        'project_id',
        'project_name',
        'axis_id',
        'axis_name',
        'responsible_id',
        'responsible_name',
        'organization',
        'start_date',
        'end_date',
        'polygon_name',
        'location_name',
        'data_collector_name',
        'products_count',
        'population_count',
        'beneficiaries_count',
    ];
}
