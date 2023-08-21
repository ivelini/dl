<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      if (!Schema::hasColumn('products', 'additional')) {
        Schema::table('products', function (Blueprint $table) {
          $table->longText('additional')->after('delivery_weekdays')->nullable();
        });
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      if (Schema::hasColumn('products', 'additional')) {
        Schema::table('products', function (Blueprint $table) {
          $table->dropColumn('additional');
        });
      }
    }
}
