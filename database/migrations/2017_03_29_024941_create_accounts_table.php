<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('idx');
            $table->enum('type', [
                'user',
                'partner',
                'admin',
            ])->default('user');
            $table->string('email', 60)->unique()->nullable();
            $table->string('password', 60)->nullable();
            $table->timestamps();
        });

        Schema::create('accounts_users', function (Blueprint $table) {
            $table->increments('idx');
            $table->integer('account_idx');
            $table->enum('gender', [
                'male',
                'female',
                'androgyne',
                'trigender',
                'agender',
                'genderfluid'
            ])->nullable();

            $table->timestamp('birth')
                ->nullable();

            $table->enum('join_type', [
                'general',
                'facebook',
                'kakao',
                'naver',
                'twitter'
            ]);

            $table->foreign('account_idx')
                ->references('idx')
                ->on('accounts')
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
        Schema::drop('accounts');
    }
}
