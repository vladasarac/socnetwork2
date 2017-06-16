<?php

namespace App\Http\Controllers\Auth;

use App\Profile;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:users', // ja dodao da user name mora biti unique
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'gender' => 'required|bool' // polje gender ja dodao
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //ako je $data['gender'] = 1 onda je muski user i dajemo mu po difoltu avatar male.png
        if($data['gender']){
          $avatar = 'public/defaults/avatars/male.png';   
        }else{ //ako je $data['gender'] = 0 onda je zena user i dajemo mu po difoltu avatar female.png
          $avatar = 'public/defaults/avatars/female.png';  
        }
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'gender' => $data['gender'], // dodajemo da popunjava gender kolonu u 'users' tabeli
            'password' => bcrypt($data['password']),
            //dodajemo kolone slug i avatar
            'slug' => str_slug($data['name']), //od onoga sto je uneto u name polje pri registraciji pravimo slug
            'avatar' => $avatar // ovde upisujemo ako je male male.png a ako je female female.png
        ]);
        //pravimo red u 'profiles' tabeli i popunjavamo kolonu user_id id-em usera kog smo upravo kreirali
        Profile::create(['user_id' => $user->id]);
        return $user;
    }
}
