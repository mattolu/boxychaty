<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Annotation extends Model 
//implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'filename','status','filelocation', 'member_id'
    ];

    public function member(){
        return $this->belongsTo(Member::class);
    }
}
