<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Specialite;


class Cursus extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'niveau',
        'annee_diplome',
        'countryedu_id',
        'cityedu_id',
        'systemedu_id',
        'typedu',
        'specialite_id',
        'school',
        'mes_formations_uir'

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function specialite()
    {
        return $this->belongsTo(Specialite::class);

    }


    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
