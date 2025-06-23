<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTime extends Model
{
    protected $table = 'deliverytime';

    protected $fillable = [
        'supplier', 'day', 'start_hours','start_minutes', 'end_hours','end_minutes', 'delivery_time', 'delivery_type'
    ];

    public $timestamps = false;

    public static function getBySupplierAndType($supplierId = null, $deliveryType = null)
    {
        $query = self::select([
            'id as itemid',
            'supplier',
            'day',
            'start_hours',
            'start_minutes',
            'end_hours',
            'end_minutes',
            'delivery_time',
            'delivery_type'
        ])->orderBy('id', 'asc');

        if (is_numeric($supplierId)) {
            $query->where('supplier', $supplierId);
        }

        if (!is_null($deliveryType)) {
            $query->where('delivery_type', $deliveryType);
        }

        return $query->get()->toArray();
    }
}