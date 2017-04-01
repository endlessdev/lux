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

            $table->engine = 'InnoDB';

            $table->increments('idx');
            $table->enum('type', [
                'user',
                'admin',
            ])->default('user');
            $table->string('email', 100)->unique()->nullable();
            $table->string('password', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('accounts_users', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('idx');
            $table->integer('account_idx')->unsigned();
            $table->string('username');
            $table->timestamp('birth')
                ->nullable();
            $table->enum('gender', [
                'male',
                'female',
                'androgyne',
                'trigender',
                'agender',
                'genderfluid'
            ])->nullable();


            $table->enum('join_type', [
                'general',
                'facebook',
                'kakao',
                'naver',
                'twitter'
            ])->default('general');

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

        Schema::table('accounts_users', function (Blueprint $table) {
            $table->dropForeign(['account_idx']);
        });

        Schema::drop('accounts_users');

        Schema::drop('accounts');
    }
}
