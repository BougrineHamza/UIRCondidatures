<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Stat extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_login','last_ip','total_connexions','lead_level','total_time_spent','signup_trafic_source'
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
