<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_postal_code', 8)->after('payment_method');
            $table->string('shipping_address', 255)->after('shipping_postal_code');
            $table->string('shipping_building_name', 255)->nullable()->after('shipping_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_postal_code',
                'shipping_address',
                'shipping_building_name',
            ]);
        });
    }
}
