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
		Schema::create(config('cart.table_name') ?? 'laracart', function (Blueprint $table) {
			$table->id();
			$table->string('product_id');
			$table->string('name');
			$table->integer('quantity');
			$table->string('price');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists(config('cart.table_name') ?? 'laracart');
	}
};
