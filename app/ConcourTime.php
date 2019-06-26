<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ConcourDate;
use App\Jury;
use App\Convocation;
use App\User;
use App\UirFormation;

class ConcourTime extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'concour_date_id','time','user_id','jury_id','dispo','concour_time_id'

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];


    public function concourdate()
    {
        return $this->belongsTo(ConcourDate::class, 'concour_date_id');

    }

    public function jury()
    {
        return $this->belongsTo(Jury::class);

    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function convocation()
    {
        return $this->hasOne(Convocation::class);

    }


    public function uirformation()
    {
        return $this->belongsTo(UirFormation::class,'uir_formation_id');

    }

}
