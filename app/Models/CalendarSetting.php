<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarSetting extends Model
{
    use HasFactory;

    protected $table = 'calendar_setting';

    protected $primaryKey = 'calendar_setting_id';

    public $timestamps = false;
    protected $casts = [
        'open_close_hours' => 'array', // Will automatically cast serialized JSON data to array
        'block_fitting_type_days' => 'array',
        'block_service_hours_date' => 'array',
    ];

    protected $fillable = [
        'calendar_name',
        'open_close_hours',
        'block_date_time',
        'default',
        'duration',
        'calendar_type',
        'holidays',
        'default_pre_booking_hours',
        'slot_perday_booking',
        'mot_perday_booking',
        'block_service_perdays',
        'block_service_perhours',
        'block_fitting_type_days',
        'block_service_hours_date',
    ];
    public function getBlockDateTimeAttribute($value)
    {
        return $value ? unserialize($value) : [];
    }

    // Automatically serialize block_date_time when saving
    public function setBlockDateTimeAttribute($value)
    {
        $this->attributes['block_date_time'] = $value ? serialize($value) : null;
    }
    public function getHolidaysAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    // Automatically serialize 'holidays' before saving
    public function setHolidaysAttribute($value)
    {
        $this->attributes['holidays'] = !empty($value) ? serialize($value) : null;
    }

    public function getBlockServicePerdaysAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    // Serialize 'block_service_perdays' before saving
    public function setBlockServicePerdaysAttribute($value)
    {
        $this->attributes['block_service_perdays'] = !empty($value) ? serialize($value) : null;
    }

    // Unserialize 'block_service_perhours' when retrieving
    public function getBlockServicePerhoursAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    // Serialize 'block_service_perhours' before saving
    public function setBlockServicePerhoursAttribute($value)
    {
        $this->attributes['block_service_perhours'] = !empty($value) ? serialize($value) : null;
    }
}
