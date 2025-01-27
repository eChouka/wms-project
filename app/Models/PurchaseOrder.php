<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table='purchase_orders';
    protected $fillable = [
        'ref',
    ];

    
    
    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [            'id' => 'User',
            'purchase_order_id' => 'User',
            'supplier_id' => 'Supplier',
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getRelationMappings()
    {
       return $relationMap = [            'id' => 'User',
            'purchase_order_id' => 'User',
            'supplier_id' => 'Supplier',
        ];
    }


    


    

    

    public static function readableColumnNames()
    {
        return [
            'ref' => 'Reference',
            'user_id'=>'User',
            'total'=>'Total',
            'supplier_id'=>'Supplier'
        ];
    }

    public static function requiredFields()
    {
        return [
            'ref',
            'user_id',
            'total',
            'supplier_id',
        ];
    }

    
    
    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }



    
    
    public function PurchaseOrderItems()
    {
        return $this->hasOne(User::class, 'purchase_order_id', 'id');
    }



    
    
    public function Suppplier()
    {
        return $this->hasOne(Supplier::class, 'supplier_id', 'id');
    }




    



    
    public static function getRelationMappingConfig()
    {
        return [
            'sourceModel' => [
                
            ],
            'targetModel' => [
                
            ],
            'fieldMapping' => [
                
            ],
        ];
    }




    public function getHasManyRelations()
    {
        return [        ];
    }

}