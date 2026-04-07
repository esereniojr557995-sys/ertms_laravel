<?php
// app/Traits/LogsActivity.php
namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected function logActivity(string $action, string $module, mixed $recordId = null, string $details = null): void
    {
        AuditLog::create([
            'user_id'   => Auth::id(),
            'action'    => $action,
            'module'    => $module,
            'record_id' => $recordId ? (string) $recordId : null,
            'ip_address'=> Request::ip(),
            'details'   => $details,
            'created_at'=> now(),
        ]);
    }
}
