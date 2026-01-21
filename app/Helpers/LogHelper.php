<?php

namespace App\Helpers;

use App\Models\LogHistory;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     *
     * @param string $sourceTable
     * @param int|null $entityId
     * @param string $action
     * @param array|null $oldData
     * @param array|null $newData
     */
    public static function logAction($sourceTable, $entityId = null, $action, $oldData = null, $newData = null)
    {
        LogHistory::create([
            'source_table' => $sourceTable,
            'entity_id'    => $entityId,
            'action'       => $action,
            'logged_at'    => now(),
            'user'         => Auth::user()->name ?? 'system',
            'old_data'     => $oldData ? json_encode($oldData) : null,
            'new_data'     => $newData ? json_encode($newData) : null,
        ]);
    }
}
