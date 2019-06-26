<?php

namespace App;

use App\User;
use App\Localisation;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gender',
        'cin',
        'country_id',
        'city_id',
        'address',
        'phone',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
