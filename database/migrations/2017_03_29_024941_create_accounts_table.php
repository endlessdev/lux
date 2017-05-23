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
            $table->timestamp('deleted_at');
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

            $table->timestamp('updated_at')
            ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));


            $table->enum('join_type', [
                'general',
                'facebook',
                'kakao',
//                'naver',
                'twitter'
            ])->default('general');

            $table->foreign('account_idx')
                ->references('idx')
                ->on('accounts')
                ->onDelete('cascade');
        });

        Schema::create('accounts_users_fb', function(Blueprint $table){
            $table->increments('idx');
            $table->integer('account_idx')->unsigned();

            $table->string('fb_id');
            $table->string('fb_token');

            $table->foreign('account_idx')
                ->references('idx')
                ->on('accounts')
                ->onDelete('cascade');
        });

        Schema::create('accounts_users_kakao', function(Blueprint $table){
            $table->increments('idx');
            $table->integer('account_idx')->unsigned();

            $table->string('kakao_id');
            $table->string('kakao_token');

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

        Schema::table('accounts_users_app', function (Blueprint $table) {
            $table->dropForeign(['account_idx']);
        });

        Schema::drop('accounts');

        Schema::drop('accounts_users');

        Schema::drop('accounts');
    }
}
