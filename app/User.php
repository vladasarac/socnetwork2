<?php

namespace App;

use Storage;
use App\Traits\Friendable;
use Laravel\Scout\Searchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Friendable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'slug', 'gender', 'avatar'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //
    //public $with = ['friends'];

    //pravimo one-to-one relaciju sa 'profiles' tabelom posto jedan user moze imati jedan profil
    public function profile(){
      return $this->hasOne('App\Profile');  
    }

    //one-to-many relacija sa 'posts' tabelom posto jedan user ima vise postova(ako hoce)
    public function posts(){
      return $this->hasMany('App\Post');  
    }

    //ovo je accessor koji ispred imena slike tj avatara ispred dodaje http://socnetwork2.dev/storage/ 
    public function getAvatarAttribute($avatar){
      return asset(Storage::url($avatar)); 
    }

    //ja dodo
    // public function friends(){
    //    return $this->hasMany('App\Friendship');   
    // }
    
}
