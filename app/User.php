<?php

namespace App;
// use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Profil;
use App\Stat;
use App\Cursus;
use App\Convocation;
use App\ConcourTime;
// use App\Cloned;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;//, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email','password','api_token', 'uir_student'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'updated_at'
    ];


    public function profil()
    {
        return $this->hasOne(Profil::class);

    }

    public function stat()
    {
        return $this->hasOne(Stat::class);

    }

    public function cursus()
    {
        return $this->hasOne(Cursus::class);

    }


    public function convocation()
    {
        return $this->hasMany(Convocation::class);

    }

    public function concourtime()
    {
        return $this->hasMany(ConcourTime::class);

    }

    // public function clone()
    // {
    //     return $this->hasOne(Clone::class);

    // }

}
