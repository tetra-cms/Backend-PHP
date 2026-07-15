<?php

use App\Enums\DeliveryTypes;
use App\Enums\OrderStatus;
use App\Enums\PaymentTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('comment')
                ->nullable();

            $table->enum(
                'status',
                array_column(
                    OrderStatus::cases(),
                    'value'
                )
            )->default(
                OrderStatus::InProgress->value
            );

            $table->enum(
                'delivery_type',
                array_column(
                    DeliveryTypes::cases(),
                    'value'
                )
            )->default(
                DeliveryTypes::pickup->value
            );

            $table->enum(
                'payment_type',
                array_column(
                    PaymentTypes::cases(),
                    'value'
                )
            )->default(
                PaymentTypes::cash->value
            );

            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
