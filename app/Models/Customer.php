<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table='customers';

    protected $fillable = [
        'name',
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
            'billing_address_1' => 'Billing Address 1',
            'billing_address_2' => 'Billing Address 2',
            'billing_town' => 'Billing Town',
            'billing_postcode' => 'Billing Postcode',
            'billing_county' => 'BillingCounty',
            'billing_country' => 'Billing Country',
            'delivery_address_1'=>'Delivery Address 1',
            'delivery_address_2'=>'Delivery Address 2',
            'delivery_town'=>'Delivery Town',
            'delivery_postcode'=>'Delivery Postcode',
            'delivery_county'=>'Delivery County',
            'delivery_country'=>'Delivery Country',
        ];
    }

    public static function requiredFields()
    {
        return [
            'name',
            'billing_address_1',
            'billing_town',
            'billing_postcode',
            'billing_country',
        ];
    }

}
