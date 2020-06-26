<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admins_id')->unsigned()->default(0);
            $table->string('company_name')->default('');
            $table->string('plans_id')->default('');
            $table->string('stripe_id')->default('');
            $table->string('card_brand')->default('');
            $table->string('card_last_four')->default('');
            $table->timestamp('trial_ends_at')->nullable();
            $table->tinyInteger('owner')->unsigned()->default(0);
            $table->tinyInteger('type')->unsigned()->default(2);
            $table->string('email')->default('');
            $table->string('password')->default('');
            $table->string('firstname')->default('');
            $table->string('lastname')->default('');
            $table->string('phone')->default('');
            $table->string('view_phone')->default('');
            $table->tinyInteger('offset')->default(0);
            $table->string('remember_token')->default('');
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
        Schema::dropIfExists('users');
    }
}
