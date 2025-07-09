<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannedProducts extends Model
{
    protected $table = 'vw_planned_products';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'specific_objective_id',
        'activity_id',
        'responsible_id',
        'is_product',
        'is_population',
        'description',
        'unit',
        'year',
        'month',
        'target_value',
        'real_value',
        'data_collector_id',
        'created_at',
        'updated_at',
        'activity_name',
        'responsible_name',
    ];
}
