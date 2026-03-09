<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->date('recorded_at');
            $table->timestamps();

            $table->index(['product_id', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_histories');
    }
}
