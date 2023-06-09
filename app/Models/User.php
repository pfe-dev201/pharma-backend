<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //relationship
    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault();
    }

    public function entrees_medicaments()
    {
        return $this->hasMany(Entree::class);
    }

    public function sorties_medicaments()
    {
        return $this->hasMany(Sortie::class);
    }

    public function configuration()
    {
        return $this->hasOne(Configuration::class)->withDefault();
    }

    //methodes
    public function getRole() {
        if ($this->role->id === 1) {
            return "ECRIRE-LIRE";
        } else if ($this->role->id === 2) {
            return "ECRIRE";
        } else if ($this->role->id === 3) {
            return "LIRE";
        }
    }
}
