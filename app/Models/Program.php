<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'axe_id',
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
            'axe_id' => 'integer',
        ];
    }

    public function axe(): BelongsTo
    {
        return $this->belongsTo(Axe::class);
    }

    public function actionLines(): HasMany
    {
        return $this->hasMany(ActionLine::class);
    }

    public function programIndicators(): HasMany
    {
        return $this->hasMany(ProgramIndicator::class);
    }
}
