<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Consumption
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property float|null $h1
 * @property float|null $h2
 * @property float|null $h3
 * @property float|null $h4
 * @property float|null $h5
 * @property float|null $h6
 * @property float|null $h7
 * @property float|null $h8
 * @property float|null $h9
 * @property float|null $h10
 * @property float|null $h11
 * @property float|null $h12
 * @property float|null $h13
 * @property float|null $h14
 * @property float|null $h15
 * @property float|null $h16
 * @property float|null $h17
 * @property float|null $h18
 * @property float|null $h19
 * @property float|null $h20
 * @property float|null $h21
 * @property float|null $h22
 * @property float|null $h23
 * @property float|null $h24
 * @property float|null $h25
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Consumption betweenDates(string $startDate, string $endDate)
 */
class Consumption extends Model
{
    use HasFactory;

    protected $table = 'consumptions';

    protected $fillable = [
        'date',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'h7', 'h8', 'h9', 'h10',
        'h11', 'h12', 'h13', 'h14', 'h15', 'h16', 'h17', 'h18', 'h19', 'h20',
        'h21', 'h22', 'h23', 'h24', 'h25',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'h1' => 'float',
        'h2' => 'float',
        'h3' => 'float',
        'h4' => 'float',
        'h5' => 'float',
        'h6' => 'float',
        'h7' => 'float',
        'h8' => 'float',
        'h9' => 'float',
        'h10' => 'float',
        'h11' => 'float',
        'h12' => 'float',
        'h13' => 'float',
        'h14' => 'float',
        'h15' => 'float',
        'h16' => 'float',
        'h17' => 'float',
        'h18' => 'float',
        'h19' => 'float',
        'h20' => 'float',
        'h21' => 'float',
        'h22' => 'float',
        'h23' => 'float',
        'h24' => 'float',
        'h25' => 'float',
    ];

    /**
     * Scope Eloquent para filtrar registros por un rango de fechas inclusive.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
