
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'is_insured')) {
                $table->boolean('is_insured')->default(false);
            }

            if (!Schema::hasColumn('packages', 'declared_value')) {
                $table->decimal('declared_value', 15, 2)->nullable();
            }

            if (!Schema::hasColumn('packages', 'insurance_cost')) {
                $table->decimal('insurance_cost', 15, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'insurance_cost')) {
                $table->dropColumn('insurance_cost');
            }

            if (Schema::hasColumn('packages', 'declared_value')) {
                $table->dropColumn('declared_value');
            }

            if (Schema::hasColumn('packages', 'is_insured')) {
                $table->dropColumn('is_insured');
            }
        });
    }
};
