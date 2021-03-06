<?php

namespace App;

use App\User;
use App\ConcourDate;
use Illuminate\Database\Eloquent\Model;

class Convocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

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

    public function concourdate()
    {
        return $this->hasMany(ConcourDate::class);
    }
}
