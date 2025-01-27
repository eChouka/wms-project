<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    protected $table='stock_movements';
    protected $fillable = [
        'product_id',
        'moved_from_location_id',
        'moved_to_location_id',
        'sales_order_id',
        'purchase_order_id',
        'qty'
    ];

    public function getHasManyRelations()
    {
        return [
        ];
    }


    public static function readableColumnNames()
    {
        return [
            'product_id' => 'Product',
            'moved_from_location_id' => 'Moved From Location',
            'moved_to_location_id' => 'Moved to Location',
            'sales_order_id'=>'Sales Order',
            'purchase_order_id'=>'Purchase Order',
            'qty'=>'Quantity'
        ];
    }

    public static function requiredFields()
    {
        return [
            'product_id',
            'moved_to_location_id',
            'moved_from_location_id',
            'qty',
        ];
    }

    public static function getRelationMethodName()
    {
       return [];
    }


    public function Product()
    {
        return $this->hasOne(Product::class, 'product_id', 'id');
    }

    public function PurchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function SalesOrder()
    {
        return $this->hasOne(SalesOrder::class, 'sales_order_id', 'id');
    }

    public function MoveFromLocation()
    {
        return $this->hasOne(Location::class, 'moved_from_location_id', 'id');
    }

    public function MoveToLocation()
    {
        return $this->hasOne(Location::class, 'moved_to_location_id', 'id');
    }
}
