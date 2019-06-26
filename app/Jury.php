<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ConcourTime;
use App\UirEcole;

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
        'phone'

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
