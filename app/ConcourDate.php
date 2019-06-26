<?php

namespace App;

use App\ConcourTime;

use App\UirFormation;
use Illuminate\Database\Eloquent\Model;

class ConcourDate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uirformation_id', 'date_concours',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function uirformation()
    {
        return $this->belongsTo(UirFormation::class);
    }

    public function concourtime()
    {
        return $this->hasMany(ConcourTime::class);
    }
}
