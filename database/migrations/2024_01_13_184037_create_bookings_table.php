<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger("user_id")->nullable(false);
            $table->unsignedBigInteger("car_id")->nullable(false);
            $table->timestamps();



            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("car_id")->references("id")->on("cars")->onDelete("cascade");

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
