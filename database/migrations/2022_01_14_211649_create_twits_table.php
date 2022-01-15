<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twits', function (Blueprint $table) {
            $table->id();
            $table->char('twitter_account', 15)->index();
            $table->string('twit', 1000);
            $table->boolean('status')->default(false);
            $table->date('date');
            $table->timestamps();

            $table->foreign('twitter_account')
                ->references('twitter_account')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twits');
    }
}
