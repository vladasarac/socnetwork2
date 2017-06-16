<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requester'); //id korisnika koji nekome drugom korisniku saalje zahtev za prijateljstvo
            $table->integer('user_requested');//id korisnika kom je zahtev za prijateljstvo upucen
            $table->boolean('status')->default(0);//ako je 1 onda su prijatelji ako je 0 onda nisu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friendships');
    }
}
