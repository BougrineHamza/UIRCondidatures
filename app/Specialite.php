<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cursus;

class Specialite extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'uirformation_id_map'

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public $timestamps = false;
    


    public function cursus()
    {
        return $this->hasOne(Cursus::class);

    }
}
