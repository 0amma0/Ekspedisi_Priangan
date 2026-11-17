<?php

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
        Schema::create('couriers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained();
            $t->string('code')->unique();
            $t->string('name');
            $t->string('phone');
            $t->boolean('is_active')->default(true);
            $t->unsignedTinyInteger('max_packages')->default(20);
            $t->timestamps();
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone');
            $t->text('address');
            $t->timestamps();
        });

        Schema::create('zones', function (Blueprint $t) {
            $t->id();
            $t->string('name');
        });

        Schema::create('zone_rates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('zone_id')->unique()->constrained('zones');
            $t->unsignedInteger('price_per_kg');
            $t->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $t->timestamps();
        });

        Schema::create('daily_sequences', function (Blueprint $t) {
            $t->date('sequence_date')->primary();
            $t->unsignedInteger('last_number')->default(0);
        });

        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('tracking_number')->unique();
            $t->date('received_date')->index();
            $t->foreignId('customer_id')->constrained('customers');
            $t->string('recipient_name');
            $t->string('recipient_phone');
            $t->text('recipient_address');
            $t->string('destination_city'); //isi data zona 
            $t->foreignId('zone_id')->nullable()->constrained('zones');
            $t->boolean('is_zone_manual')->default(false);
            $t->decimal('weight_kg', 8, 2)->nullable();
            $t->unsignedInteger('billable_weight')->nullable();
            $t->boolean('needs_verification')->default(false);
            $t->enum('service_type', ['Reguler', 'Express']);
            $t->unsignedInteger('rate_per_kg');
            $t->unsignedInteger('shipping_cost');
            $t->boolean('is_cod')->default(false);
            $t->unsignedInteger('cod_amount')->default(0);
            $t->unsignedInteger('cod_fee')->default(0);
            $t->enum('status', ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'])
                ->default('Diterima')->index();
            $t->unsignedTinyInteger('failed_attempts')->default(0);
            $t->foreignId('courier_id')->nullable()->constrained('couriers');
            $t->foreignId('created_by_user_id')->constrained('users');
            $t->dateTime('cod_settled_at')->nullable();
            $t->foreignId('cod_settled_by_user_id')->nullable()->constrained('users');
            $t->timestamps();
        });

        Schema::create('package_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $t->string('from_status')->nullable();
            $t->string('to_status');
            $t->foreignId('actor_user_id')->nullable()->constrained('users');
            $t->string('actor_role');
            $t->foreignId('courier_id')->nullable()->constrained('couriers');
            $t->text('notes')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_histories');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('daily_sequences');
        Schema::dropIfExists('zone_rates');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('couriers');
    }
};
