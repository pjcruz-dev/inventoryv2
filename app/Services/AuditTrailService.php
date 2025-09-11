<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class AuditTrailService
{
    private const OPERATION_TYPES = [
        'import' => 'Data Import',
        'export' => 'Data Export',
        'template_download' => 'Template Download',
        'validation' => 'Data Validation',
        'rollback' => 'Transaction Rollback'
    ];
    
    private const STATUS_TYPES = [
        'started' => 'Operation Started',
        'in_progress' => 'In Progress',
        'completed' => 'Completed Successfully',
        'failed' => 'Failed',
        'rolled_back' => 'Rolled Back',
        'partial' => 'Partially Completed'
    ];
    
    private string $operationId;
    private array $operationData = [];
    private array $changes = [];
    private array $rollbackData = [];
    
    public function __construct()
    {
        $this->operationId = $this->generateOperationId();
    }
    
    /**
     * Generate unique operation ID
     */
    private function generateOperationId(): string
    {
        return 'OP_' . now()->format('YmdHis') . '_' . uniqid();
    }
    
    /**
     * Start a new operation with atomic transaction
     */
    public function startOperation(string $type, string $module, array $metadata = []): string
    {
        DB::beginTransaction();
        
        $this->operationData = [
            'operation_id' => $this->operationId,
            'type' => $type,
            'module' => $module,
            'status' => 'started',
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'started_at' => now(),
            'metadata' => $metadata,
            'changes_count' => 0,
            'errors_count' => 0,
            'warnings_count' => 0
        ];
        
        $this->logOperation('started', 'Operation started');
        $this->storeAuditRecord();
        
        return $this->operationId;
    }
    
    /**
     * Update operation status
     */
    public function updateOperationStatus(string $status, string $message = '', array $additionalData = []): void
    {
        $this->operationData['status'] = $status;
        $this->operationData['last_updated'] = now();
        
        if ($status === 'completed') {
            $this->operationData['completed_at'] = now();
            $this->operationData['duration'] = now()->diffInSeconds($this->operationData['started_at']);
        } elseif ($status === 'failed') {
            $this->operationData['failed_at'] = now();
            $this->operationData['failure_reason'] = $message;
        }
        
        $this->operationData = array_merge($this->operationData, $additionalData);
        
        $this->logOperation($status, $message);
        $this->updateAuditRecord();
    }
    
    /**
     * Record a data change for rollback purposes
     */
    public function recordChange(string $table, string $action, array $data, ?int $recordId = null): void
    {
        $changeId = uniqid();
        
        $change = [
            'change_id' => $changeId,
            'operation_id' => $this->operationId,
            'table' => $table,
            'action' => $action, // 'create', 'update', 'delete'
            'record_id' => $recordId,
            'timestamp' => now(),
            'user_id' => Auth::id()
        ];
        
        switch ($action) {
            case 'create':
                $change['new_data'] = $data;
                $change['rollback_action'] = 'delete';
                break;
                
            case 'update':
                $change['old_data'] = $this->getRecordData($table, $recordId);
                $change['new_data'] = $data;
                $change['rollback_action'] = 'update';
                break;
                
            case 'delete':
                $change['old_data'] = $data;
                $change['rollback_action'] = 'create';
                break;
        }
        
        $this->changes[] = $change;
        $this->rollbackData[] = $this->prepareRollbackData($change);
        
        $this->operationData['changes_count'] = count($this->changes);
        
        $this->logChange($change);
    }
    
    /**
     * Get current record data for rollback
     */
    private function getRecordData(string $table, ?int $recordId): ?array
    {
        if (!$recordId) {
            return null;
        }
        
        try {
            return DB::table($table)->where('id', $recordId)->first()?->toArray();
        } catch (Exception $e) {
            Log::warning('Failed to get record data for rollback', [
                'table' => $table,
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Prepare rollback data
     */
    private function prepareRollbackData(array $change): array
    {
        $rollback = [
            'change_id' => $change['change_id'],
            'table' => $change['table'],
            'action' => $change['rollback_action'],
            'record_id' => $change['record_id']
        ];
        
        switch ($change['rollback_action']) {
            case 'delete':
                $rollback['condition'] = ['id' => $change['record_id']];
                break;
                
            case 'update':
                $rollback['data'] = $change['old_data'];
                $rollback['condition'] = ['id' => $change['record_id']];
                break;
                
            case 'create':
                $rollback['data'] = $change['old_data'];
                break;
        }
        
        return $rollback;
    }
    
    /**
     * Commit the transaction and finalize operation
     */
    public function commitOperation(array $summary = []): bool
    {
        try {
            DB::commit();
            
            $this->updateOperationStatus('completed', 'Operation completed successfully', [
                'summary' => $summary,
                'total_changes' => count($this->changes)
            ]);
            
            $this->storeChangesRecord();
            
            Log::info('Operation committed successfully', [
                'operation_id' => $this->operationId,
                'changes_count' => count($this->changes),
                'summary' => $summary
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->rollbackOperation('Commit failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rollback the transaction and operation
     */
    public function rollbackOperation(string $reason = 'Operation failed'): bool
    {
        try {
            DB::rollback();
            
            $this->updateOperationStatus('rolled_back', $reason, [
                'rollback_reason' => $reason,
                'changes_rolled_back' => count($this->changes)
            ]);
            
            $this->logOperation('rolled_back', 'Transaction rolled back: ' . $reason);
            
            Log::warning('Operation rolled back', [
                'operation_id' => $this->operationId,
                'reason' => $reason,
                'changes_count' => count($this->changes)
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to rollback operation', [
                'operation_id' => $this->operationId,
                'error' => $e->getMessage(),
                'original_reason' => $reason
            ]);
            
            return false;
        }
    }
    
    /**
     * Perform manual rollback of completed operation
     */
    public function performManualRollback(string $operationId, string $reason = 'Manual rollback requested'): bool
    {
        try {
            $auditRecord = $this->getAuditRecord($operationId);
            if (!$auditRecord) {
                throw new Exception('Operation not found');
            }
            
            if ($auditRecord['status'] !== 'completed') {
                throw new Exception('Can only rollback completed operations');
            }
            
            $changes = $this->getOperationChanges($operationId);
            if (empty($changes)) {
                throw new Exception('No changes found to rollback');
            }
            
            DB::beginTransaction();
            
            // Start rollback operation
            $rollbackOpId = $this->startOperation('rollback', $auditRecord['module'], [
                'original_operation_id' => $operationId,
                'rollback_reason' => $reason
            ]);
            
            // Execute rollback in reverse order
            $rollbackChanges = array_reverse($changes);
            $successCount = 0;
            
            foreach ($rollbackChanges as $change) {
                try {
                    $this->executeRollbackChange($change);
                    $successCount++;
                } catch (Exception $e) {
                    Log::error('Failed to rollback change', [
                        'change_id' => $change['change_id'],
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }
            
            // Update original operation status
            $this->updateOriginalOperationStatus($operationId, 'rolled_back', $reason);
            
            $this->commitOperation([
                'original_operation_id' => $operationId,
                'changes_rolled_back' => $successCount,
                'rollback_reason' => $reason
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->rollbackOperation('Manual rollback failed: ' . $e->getMessage());
            
            Log::error('Manual rollback failed', [
                'operation_id' => $operationId,
                'error' => $e->getMessage(),
                'reason' => $reason
            ]);
            
            return false;
        }
    }
    
    /**
     * Execute a single rollback change
     */
    private function executeRollbackChange(array $change): void
    {
        $rollbackData = json_decode($change['rollback_data'], true);
        
        switch ($rollbackData['action']) {
            case 'delete':
                DB::table($rollbackData['table'])
                    ->where($rollbackData['condition'])
                    ->delete();
                break;
                
            case 'update':
                DB::table($rollbackData['table'])
                    ->where($rollbackData['condition'])
                    ->update($rollbackData['data']);
                break;
                
            case 'create':
                DB::table($rollbackData['table'])
                    ->insert($rollbackData['data']);
                break;
        }
        
        $this->recordChange($rollbackData['table'], $rollbackData['action'], $rollbackData['data'] ?? [], $rollbackData['record_id'] ?? null);
    }
    
    /**
     * Record error in operation
     */
    public function recordError(string $message, array $context = []): void
    {
        $this->operationData['errors_count'] = ($this->operationData['errors_count'] ?? 0) + 1;
        
        Log::error('Operation Error', array_merge([
            'operation_id' => $this->operationId,
            'message' => $message
        ], $context));
    }
    
    /**
     * Record warning in operation
     */
    public function recordWarning(string $message, array $context = []): void
    {
        $this->operationData['warnings_count'] = ($this->operationData['warnings_count'] ?? 0) + 1;
        
        Log::warning('Operation Warning', array_merge([
            'operation_id' => $this->operationId,
            'message' => $message
        ], $context));
    }
    
    /**
     * Store audit record in database
     */
    private function storeAuditRecord(): void
    {
        try {
            DB::table('audit_trails')->insert([
                'operation_id' => $this->operationData['operation_id'],
                'type' => $this->operationData['type'],
                'module' => $this->operationData['module'],
                'status' => $this->operationData['status'],
                'user_id' => $this->operationData['user_id'],
                'user_name' => $this->operationData['user_name'],
                'ip_address' => $this->operationData['ip_address'],
                'user_agent' => $this->operationData['user_agent'],
                'started_at' => $this->operationData['started_at'],
                'metadata' => json_encode($this->operationData['metadata']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to store audit record', [
                'operation_id' => $this->operationId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update existing audit record
     */
    private function updateAuditRecord(): void
    {
        try {
            $updateData = [
                'status' => $this->operationData['status'],
                'changes_count' => $this->operationData['changes_count'] ?? 0,
                'errors_count' => $this->operationData['errors_count'] ?? 0,
                'warnings_count' => $this->operationData['warnings_count'] ?? 0,
                'updated_at' => now()
            ];
            
            if (isset($this->operationData['completed_at'])) {
                $updateData['completed_at'] = $this->operationData['completed_at'];
                $updateData['duration'] = $this->operationData['duration'];
            }
            
            if (isset($this->operationData['failed_at'])) {
                $updateData['failed_at'] = $this->operationData['failed_at'];
                $updateData['failure_reason'] = $this->operationData['failure_reason'];
            }
            
            if (isset($this->operationData['summary'])) {
                $updateData['summary'] = json_encode($this->operationData['summary']);
            }
            
            DB::table('audit_trails')
                ->where('operation_id', $this->operationId)
                ->update($updateData);
                
        } catch (Exception $e) {
            Log::error('Failed to update audit record', [
                'operation_id' => $this->operationId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Store changes record
     */
    private function storeChangesRecord(): void
    {
        if (empty($this->changes)) {
            return;
        }
        
        try {
            foreach ($this->changes as $change) {
                DB::table('audit_changes')->insert([
                    'operation_id' => $this->operationId,
                    'change_id' => $change['change_id'],
                    'table_name' => $change['table'],
                    'action' => $change['action'],
                    'record_id' => $change['record_id'],
                    'old_data' => isset($change['old_data']) ? json_encode($change['old_data']) : null,
                    'new_data' => isset($change['new_data']) ? json_encode($change['new_data']) : null,
                    'rollback_data' => json_encode($this->rollbackData[array_search($change, $this->changes)]),
                    'user_id' => $change['user_id'],
                    'created_at' => $change['timestamp'],
                    'updated_at' => now()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to store changes record', [
                'operation_id' => $this->operationId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Log operation event
     */
    private function logOperation(string $status, string $message): void
    {
        Log::info('Audit Trail Operation', [
            'operation_id' => $this->operationId,
            'type' => $this->operationData['type'],
            'module' => $this->operationData['module'],
            'status' => $status,
            'message' => $message,
            'user_id' => $this->operationData['user_id'],
            'changes_count' => count($this->changes)
        ]);
    }
    
    /**
     * Log individual change
     */
    private function logChange(array $change): void
    {
        Log::debug('Audit Trail Change', [
            'operation_id' => $this->operationId,
            'change_id' => $change['change_id'],
            'table' => $change['table'],
            'action' => $change['action'],
            'record_id' => $change['record_id']
        ]);
    }
    
    /**
     * Get audit record by operation ID
     */
    public function getAuditRecord(string $operationId): ?array
    {
        try {
            $record = DB::table('audit_trails')
                ->where('operation_id', $operationId)
                ->first();
                
            return $record ? (array) $record : null;
        } catch (Exception $e) {
            Log::error('Failed to get audit record', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get operation changes
     */
    public function getOperationChanges(string $operationId): array
    {
        try {
            return DB::table('audit_changes')
                ->where('operation_id', $operationId)
                ->orderBy('created_at')
                ->get()
                ->toArray();
        } catch (Exception $e) {
            Log::error('Failed to get operation changes', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Update original operation status
     */
    private function updateOriginalOperationStatus(string $operationId, string $status, string $reason): void
    {
        try {
            DB::table('audit_trails')
                ->where('operation_id', $operationId)
                ->update([
                    'status' => $status,
                    'rollback_reason' => $reason,
                    'rolled_back_at' => now(),
                    'updated_at' => now()
                ]);
        } catch (Exception $e) {
            Log::error('Failed to update original operation status', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get operation statistics
     */
    public function getOperationStatistics(array $filters = []): array
    {
        try {
            $query = DB::table('audit_trails');
            
            if (isset($filters['date_from'])) {
                $query->where('started_at', '>=', $filters['date_from']);
            }
            
            if (isset($filters['date_to'])) {
                $query->where('started_at', '<=', $filters['date_to']);
            }
            
            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }
            
            if (isset($filters['module'])) {
                $query->where('module', $filters['module']);
            }
            
            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
            
            $stats = $query->selectRaw('
                COUNT(*) as total_operations,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_operations,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_operations,
                SUM(CASE WHEN status = "rolled_back" THEN 1 ELSE 0 END) as rolled_back_operations,
                SUM(changes_count) as total_changes,
                AVG(duration) as avg_duration,
                MAX(duration) as max_duration
            ')->first();
            
            return (array) $stats;
            
        } catch (Exception $e) {
            Log::error('Failed to get operation statistics', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Get current operation ID
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }
    
    /**
     * Get current operation data
     */
    public function getOperationData(): array
    {
        return $this->operationData;
    }
    
    /**
     * Get recorded changes
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}