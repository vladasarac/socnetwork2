<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    //------------------------------------------------------------------------------------------------------------------------------------

    //metod poziva ruta /notifications da neprocitane notifikacije usera koji je pozvao metod pretvori u procitane i da izvuce sve notifi-
    //kacije tog usera i da ih posalje u vju nots.blade.php da mu budu prikazane
    public function notifications(){
      //sve neprocitane notifikacije trenutno ulogovanog usera iz 'notifications' tabele pretvaramo u procitane tj kolonu read_at menjamo-
      //-iz null u datum
      Auth::user()->unreadNotifications->markAsRead();
      //vadimo sve notifikacije trenutno ulogovanog usera i saljemo ih u vju nots.blade.php da ih prikaze
      return view('nots')->with('nots', Auth::user()->notifications);    
    }


}
