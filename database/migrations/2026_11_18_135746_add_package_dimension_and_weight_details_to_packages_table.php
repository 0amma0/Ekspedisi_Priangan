
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'volumetric_weight_kg')) {
                $table->decimal('volumetric_weight_kg', 8, 2)->nullable();
            }

            if (!Schema::hasColumn('packages', 'base_weight_kg')) {
                $table->decimal('base_weight_kg', 8, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('packages', 'volumetric_weight_kg')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('volumetric_weight_kg');
            });
        }

        if (Schema::hasColumn('packages', 'base_weight_kg')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('base_weight_kg');
            });
        }
    }
};
