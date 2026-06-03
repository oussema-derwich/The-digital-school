<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('crypto_id');
            $table->string('name');
            $table->decimal('price_alert', 16, 8);
            $table->string('condition');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('crypto_id')->references('id')->on('cryptocurrencies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
}
