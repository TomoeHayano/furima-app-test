<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product_category', function (Blueprint $table) {
      $table->id();
      $table->string('product_id', 10);
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
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
    Schema::dropIfExists('product_category');
  }
}
