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
        'open_close_hours' => 'array',
        'block_fitting_type_days' => 'array',
        'block_service_hours_date' => 'array',
        'block_fitting_type_datetime' => 'array',
        'block_service_datetime' => 'array',
    ];

    protected $fillable = [
        'calendar_name',
        'open_close_hours',
        'garage_id',
        'am_pm_break_point',
        'block_date_time',
        'block_specific_datetime',
        'ramps_block_day_time',
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
        'block_fitting_type_datetime',
        'block_service_datetime',
    ];
    public function getBlockDateTimeAttribute($value)
    {
        return $value ? unserialize($value) : [];
    }

    public function setBlockDateTimeAttribute($value)
    {
        $this->attributes['block_date_time'] = $value ? serialize($value) : null;
    }

    public function getBlockSpecificDateTimeAttribute($value)
    {
        return $value ? unserialize($value) : [];
    }
    public function setBlockSpecificDateTimeAttribute($value)
    {
        $this->attributes['block_specific_datetime'] = $value ? serialize($value) : null;
    }
    public function getRampsBlockDayTimeAttribute($value)
    {
        return $value ? unserialize($value) : [];
    }
    public function setRampsBlockDayTimeAttribute($value)
    {
        $this->attributes['ramps_block_day_time'] = $value ? serialize($value) : null;
    }

    public function getHolidaysAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    public function setHolidaysAttribute($value)
    {
        $this->attributes['holidays'] = !empty($value) ? serialize($value) : null;
    }

    public function getBlockServicePerdaysAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    public function setBlockServicePerdaysAttribute($value)
    {
        $this->attributes['block_service_perdays'] = !empty($value) ? serialize($value) : null;
    }

    public function getBlockServicePerhoursAttribute($value)
    {
        return !empty($value) ? unserialize($value) : [];
    }

    public function setBlockServicePerhoursAttribute($value)
    {
        $this->attributes['block_service_perhours'] = !empty($value) ? serialize($value) : null;
    }

    public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id');
    }

}
