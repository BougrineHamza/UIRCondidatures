<?php

namespace App;

use App\Admin;
use App\UirFormation;
use Illuminate\Database\Eloquent\Model;

class UirEcole extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'logo_path',
        'color',
        'titre',

    ];

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function uirformation()
    {
        return $this->hasMany(UirFormation::class);
    }

    public function admin()
    {
        return $this->hasMany(Admin::class);
    }
}
