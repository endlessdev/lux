<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('idx');
            $table->integer('account_idx')->unsigned();
            $table->string('token', 50)->unique();
            $table->timestamp('expire_at');

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
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropForeign(['account_idx']);
        });
        Schema::drop('tokens');
    }
}
