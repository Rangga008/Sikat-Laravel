<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan tabel orders memiliki kolom yang diperlukan
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom payment_method jika belum ada
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['transfer', 'cod'])
                      ->after('status')
                      ->default('transfer');
            }

            // Tambahkan kolom payment_status
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'rejected'])
                      ->after('payment_method')
                      ->default('unpaid');
            }

            // Tambahkan kolom payment_proof
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_status');
            }

            // Tambahkan kolom seller_confirmation_required
            if (!Schema::hasColumn('orders', 'seller_confirmation_required')) {
                $table->boolean('seller_confirmation_required')
                      ->default(false)
                      ->nullable()
                      ->after('payment_proof');
            }

            // Tambahkan kolom verification_notes
            if (!Schema::hasColumn('orders', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('seller_confirmation_required');
            }
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus kolom jika perlu
            $table->dropColumnIfExists('payment_method');
            $table->dropColumnIfExists('payment_status');
            $table->dropColumnIfExists('payment_proof');
            $table->dropColumnIfExists('seller_confirmation_required');
            $table->dropColumnIfExists('verification_notes');
        });
    }
};