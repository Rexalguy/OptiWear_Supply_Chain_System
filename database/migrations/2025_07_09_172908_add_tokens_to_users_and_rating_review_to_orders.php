<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        /**
         * Run the migrations.
         */
        public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('tokens')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable();
            $table->text('review')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tokens');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['rating', 'review']);
        });
    }

};
