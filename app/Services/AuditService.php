<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an audit event into the immutable audit_logs table.
     *
     * @param string $action E.g. LOGIN, LOGOUT, CREATE, UPDATE, DELETE, SUBMIT, APPROVE, REJECT, PASSWORD_RESET
     * @param string|null $entityType E.g. User, Project, MilestoneReport, BudgetRequest, Evaluation, ThematicArea
     * @param int|string|null $entityId
     * @param string|null $details
     * @param int|null $userId
     * @return AuditLog
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        $entityId = null,
        ?string $details = null,
        ?int $userId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'     => $userId ?: Auth::id(),
            'action'      => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id'   => $entityId ? (int)$entityId : null,
            'details'     => $details,
            'ip_address'  => Request::ip() ?: '127.0.0.1',
        ]);
    }
}

