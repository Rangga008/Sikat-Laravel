<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankColumnsToOrdersTable extends Migration
{
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('bank_name')->nullable()->after('payment_proof');
        $table->string('account_name')->nullable()->after('bank_name');
        $table->date('transfer_date')->nullable()->after('account_name');
    });
}

    public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['bank_name', 'account_name', 'transfer_date']);
    });
}
}