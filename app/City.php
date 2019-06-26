<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;

class City extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'country_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // 'id','country_id'
    ];

    
    public $timestamps = false;


    public function country()
    {
        return $this->belongsTo(Country::class);

    }
}
