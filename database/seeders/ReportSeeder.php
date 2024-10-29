<?php

namespace Database\Seeders;

use App\Models\Reports;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReportSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Reports::create([
      'name' => 'Costumer Status',
      'component' => 'CustomerStatus',
      'max_col' => 3
    ]);

    Reports::create([
      'name' => 'Collection',
      'component' => 'CollectionLocation',
      'max_col' => 1
    ]);
   
    Reports::create([
      'name' => 'Meter Reading',
      'component' => 'MeterReading',
      'max_col' => 2
    ]);

    Reports::create([
      'name' => 'Collection Summary',
      'component' => 'CollectionCrosstab',
      'max_col' => 2
    ]);

    Reports::create([
      'name' => 'Collection Summary - Payment Center',
      'component' => 'CollectionPaymentCenter',
      'max_col' => 3
    ]);
  }
}
