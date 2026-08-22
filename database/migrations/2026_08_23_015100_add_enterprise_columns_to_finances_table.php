<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('finances', function (Blueprint $table) {
            if (!Schema::hasColumn('finances', 'category')) {
                $table->string('category')->nullable()->after('type');
            }
            if (!Schema::hasColumn('finances', 'description')) {
                $table->string('description')->nullable()->after('category');
            }
            if (!Schema::hasColumn('finances', 'pic')) {
                $table->string('pic')->nullable()->after('funding_source');
            }
            if (!Schema::hasColumn('finances', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('pic');
            }
        });
    }

    public function down(): void {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['category', 'description', 'pic', 'payment_method']);
        });
    }
};
