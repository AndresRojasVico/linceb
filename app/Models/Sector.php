<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name'];

    public function companies()
    {
        //relacion n:n con empresas usando modelo pivot
        return $this->belongsToMany(Company::class)
            ->using(CompanySector::class)
            ->withTimestamps();
    }
}
