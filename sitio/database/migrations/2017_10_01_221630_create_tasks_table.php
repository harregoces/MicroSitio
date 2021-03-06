<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcliente')->unique();
            $table->text('gtm_code')->nullable();
            $table->text('ga_code')->nullable();
            $table->text('gtmaccount')->nullable();
            $table->string('workspaceid')->nullable();
            $table->string('ga_account')->nullable();
            $table->string('ga_property')->nullable();
            $table->string('ga_view')->nullable();
            $table->string('uat_id')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
