<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Which provider caused the error: herosms | grizzlysms | fivesim | jap | system
            $table->string('provider', 50)->index();
            // What the user was trying to do: order | cancel | sms-check | boosting | balance
            $table->string('action', 50)->index();
            $table->text('error_message');
            // Extra context: country, service, order_id, retry count, etc.
            $table->json('context')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_error_logs');
    }
};
