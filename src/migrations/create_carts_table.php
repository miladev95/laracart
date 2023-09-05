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
		Schema::create(config('cart.table_name') ?? 'carts', function (Blueprint $table) {
			$table->unsignedBigInteger('product_id');
			$table->string('name');
			$table->integer('quantity');
			$table->string('price');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
		Schema::dropIfExists(config('cart.table_name') ?? 'carts');
	}
};
