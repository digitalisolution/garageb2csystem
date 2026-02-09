<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($workshopId, $action, $description, $changes = null)
    {
        ActivityLog::create([
            'workshop_id' => $workshopId,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }
}
