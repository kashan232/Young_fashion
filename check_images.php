<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (App\Models\Product::all() as $p) {
    echo "ID: {$p->id} | Name: {$p->item_name} | Image: {$p->image}\n";
    $disc = $p->activeDiscounts()->get();
    echo "  Discounts: " . $disc->count() . "\n";
    foreach ($disc as $d) {
        echo "    - Disc: {$d->discount_percentage}% / Rs.{$d->total_discount}\n";
    }
}
