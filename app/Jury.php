<?php

namespace App;

use App\UirEcole;
use App\ConcourTime;
use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public $timestamps = false;

    public function concourtime()
    {
        return $this->hasMany(ConcourTime::class);
    }

    public function uirecole()
    {
        return $this->belongsTo(UirEcole::class);
    }
}
