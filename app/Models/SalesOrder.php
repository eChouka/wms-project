<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;
    protected $table='sales_orders';
    protected $fillable = [
        'ref',
    ];

    public function getRelationMappings()
    {
        return [
            'user_id' => 'User',
            'customer_id'=>'Customer',
        ];
    }

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [
            'user_id' => 'User',
            'sales_order_id' => 'SalesOrderItems',
            'customer_id'=>'Customer'
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getHasManyRelations()
    {
        return [
            'SalesOrderItems' => $this->SalesOrderItems(),
        ];
    }

    public static function readableColumnNames()
    {
        return [
            'ref' => 'Reference',
            'user_id'=>'User',
            'total'=>'Total',
            'customer_id'=>'Customer'
        ];
    }

    public static function requiredFields()
    {
        return [
            'ref',
            'user_id',
            'total',
            'customer_id',
        ];
    }
    public static function getRelationMappingConfig(){
        return [];
    }

    public function User()
    {
        return $this->hasOn(User::class, 'id', 'user_id');
    }

    public function SalesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id', 'id');
    }

    public function Customer()
    {
        return $this->hasOn(Customer::class, 'id', 'customer_id');
    }
}
