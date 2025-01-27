<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;
    protected $table='sales_order_items';
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'qty',
        'price'
    ];

    public static function readableColumnNames()
    {
        return [
            'product_id' => 'Product',
            'qty'=>'Quantity',
            'price'=>'Price',
        ];
    }

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [
            'product_id' => 'Product',
            'sales_order_id' => 'SalesOrder',
        ];

        return $relationMap[$foreignKey] ?? null;
    }


    public function getRelationMappings()
    {
        return [
            'product_id' => 'Product',
            'sales_order_id'=>'SalesOrder'
        ];
    }

    public function SalesOrder()
    {
        return $this->hasOne(SalesOrder::class, 'id', 'sales_order_id');
    }
    public function Product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
