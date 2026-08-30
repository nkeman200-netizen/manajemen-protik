<?php
// xxxx_xx_xx_create_finances_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            // Restrict delete: User tidak bisa dihapus jika punya riwayat kas
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('restrict');
            $table->enum('type', ['income', 'expense']);
            $table->string('funding_source')->nullable();
            $table->string('title');
            $table->decimal('qty', 8, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->date('date');
            $table->timestamps();

            // Optimasi Indexing untuk performa pencarian agregat
            $table->index('event_id');
            $table->index('user_id');
            $table->index('type');
        });
    }

    public function down(): void {
        Schema::dropIfExists('finances');
    }
};