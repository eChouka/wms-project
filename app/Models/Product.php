<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table='products';
    protected $fillable = [
        'name',
        'style_code',
        'sku_code',
        'parent',
        'stock'
    ];

    public function getHasManyRelations()
    {
        return [
        ];
    }

    public static function readableColumnNames()
    {
        return [
            'name' => 'Name',
            'style_code' => 'Style Code',
            'sku_code' => 'SKU Code',
            'parent'=>'Parent Product',
            'stock'=>'Stock'
        ];
    }

    public static function requiredFields()
    {
        return [
            'name',
            'style_code',
            'sku_code',
            'parent',
            'stock'
        ];
    }

    public static function getRelationMethodName()
    {
       return [];
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'area_id', 'id');
    }

    public function salesorders()
    {
        return $this->hasMany(SalesOrder::class, 'area_id', 'id');
    }

    public function purchaseorders()
    {
        return $this->hasMany(PurchaseOrder::class, 'area_id', 'id');
    }
}
