<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UirEcole;
use App\ConcourDate;
use App\ConcourTime;

class UirFormation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'uirecole_id',
        'condition_niveau',
        'condition_specialites',
        'code_agresso',
        'prerequis_html'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public $timestamps = false;
    

    public function concourdate()
    {
        return $this->hasMany(ConcourDate::class, 'uirformation_id');
    }

    public function uirecole()
    {
        return $this->belongsTo(UirEcole::class);

    }

    public function concourtime()
    {
        return $this->hasMany(UirEcole::class);

    }
}
