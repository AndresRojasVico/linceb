<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanySector extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_sector';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'sector_id',
    ];
}
