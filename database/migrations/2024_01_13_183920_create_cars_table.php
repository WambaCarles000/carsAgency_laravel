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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string("model")->nullable(false);
            $table->text("description")->nullable(false);
            $table->decimal("price",7,2,true)->nullable(false);
            $table->unsignedBigInteger("user_id")->nullable(false);
            $table->unsignedBigInteger("manufacturer_id")->nullable(false); // Ajout de la clé étrangère
            $table->unsignedBigInteger("category_id")->nullable(false); // Ajout de la clé étrangère
            $table->timestamps();


            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("manufacturer_id")->references("id")->on("manufacturers")->onDelete("cascade");
            $table->foreign("category_id")->references("id")->on("categories")->onDelete("cascade");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
};
