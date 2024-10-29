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
        Schema::create('collection_report_locations', function (Blueprint $table) {
            $table->id();
            $table->string('collector_code');
            $table->string('cashier_code');
            $table->string('cashier_name');
            $table->integer('account_count');
            $table->integer('or_count');
            $table->integer('trans_no_count');
            $table->integer('trans_bill_count');
            $table->dateTime('current_datetime');
            $table->string('location');
            $table->decimal('amount_paid', 18, 2);
            $table->dateTime('collection_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_report_locations');
    }
};
