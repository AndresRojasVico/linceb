<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'nif',
        'contact_person',
        'address',
        'phone',
        'email',
        'is_active',
        'image_path',
    ];

    public function users()
    {
        //relacion 1:n  hasMamy (tiene muchos) una company tiene muchas users y una user pertenece a una company
        return $this->hasMany(User::class);
    }

    public function sectors()
    {
        //relacion n:n con sectores usando modelo pivot
        return $this->belongsToMany(Sector::class)
            ->using(CompanySector::class)
            ->withTimestamps();
    }
}
