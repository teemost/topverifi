<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boosting_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('jap_order_id')->nullable();
            $table->integer('service_id');
            $table->string('service_name');
            $table->string('category')->nullable();
            $table->string('link');
            $table->integer('quantity');
            $table->decimal('charge', 12, 2)->default(0);
            $table->integer('start_count')->nullable();
            $table->integer('remains')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boosting_orders');
    }
};
