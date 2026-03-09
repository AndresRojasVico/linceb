<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'image_path',
        'is_active',
        'company_id',
        'role_id',
    ];

    public function company()
    {
        //belongsTo (pertenece a) una user pertenece a una company
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        //belongsTo (pertenece a) una user pertenece a un rol
        return $this->belongsTo(Role::class);
    }

    public function projects()
    {

        return $this->belongsToMany(Project::class, 'user_projects')
            ->withPivot('project_status_id', 'notes')
            ->withTimestamps();
    }

    public function tasks()
    {
        //hasMany (tiene muchos) un usuarios  tiene muchas tareas y una tarea pertenece a un usuario
        return $this->hasMany(Task::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
