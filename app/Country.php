<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\City;

class Country extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'nationalite'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];


    public $timestamps = false;
    

    public function city()
    {
        return $this->hasMany(City::class);

    }
}
