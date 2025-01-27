<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingJob extends Model
{
    use HasFactory;
    protected $table='picking_jobs';
    protected $fillable = [
        'name',
        'sales_order_id',
        'user_id',
    ];

    public static $search_fields=['name', 'sales_order_id','user_id'];

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [
            'sales_order_id' => 'SalesOrder',
            'picking_job_id' => 'pickingItems',
            'user_id'=>'User'
        ];

        return $relationMap[$foreignKey] ?? null;
    }


    public function getHasManyRelations()
    {
        return [
            'pickingItems' => $this->pickingItems(),
            // Add more relations as needed
        ];
    }

    public static function readableColumnNames()
    {
        return [
            'name' => 'Picking name',
            'sales_order_id'=>'Sales order',
            'user_id'=>'User'
        ];
    }


    public static function requiredFields()
    {
        return [
            'name',
            'sales_order_id',
            'user_id',
        ];
    }

    public static function getRelationMappingConfig()
    {
        return [
            'sourceModel' => [
                'SalesOrderItem' => [
                    'target_field' => 'sales_order_id',  // Field in SalesOrderItem to match
                    'local_field' => 'sales_order_id',   // Field in PickingJob to match with
                ],
            ],
            'targetModel' => [
                'PickingItem' => [
                    'target_field' => 'picking_job_id',  // Foreign key in PickingItem
                    'local_field' => 'id',               // ID of the current PickingJob
                ],
            ],
            'fieldMapping' => [
                'qty' => 'qty',
                'product_id' => 'product_id'
            ],
        ];
    }

    public function SalesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }
    public function pickingItems()
    {
        return $this->hasMany(PickingItem::class, 'picking_job_id', 'id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
