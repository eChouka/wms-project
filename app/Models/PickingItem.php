<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingItem extends Model
{
    use HasFactory;
    protected $table='picking_items';
    protected $fillable = [
        'picking_job_id',
        'product_id',
        'location_id',
        'qty',
        'picked_qty',
        'status'
    ];

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [
            'picking_job_id' => 'PickingJob',
            'product_id' => 'Product',
            'location_id'=>'Location'
        ];

        return $relationMap[$foreignKey] ?? null;
    }

     public function getRelationMappings()
    {
        return [
            'product_id' => 'Product',
            'location_id'=>'Location',
            'picking_job_id'=>'PickingJob'
        ];
    }


    public static function readableColumnNames()
    {
        return [
            'picking_job_id' => 'Picking Job',
        ];
    }

    public static function requiredFields()
    {
        return [
            'picking_job_id',
            'product_id',
            'qty',
            'status',
        ];
    }
    public function PickingJob()
    {
        return $this->hasOne(PickingJob::class, 'id', 'picking_job_id');
    }
    public function Product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function Location()
    {
        return $this->hasONe(Location::class, 'id', 'location_id');
    }
}
