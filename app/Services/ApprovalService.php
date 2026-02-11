<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalLayer;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Get the layers for a specific feature and department.
     */
    public function getLayers($feature, $kodeDept = null)
    {
        $query = ApprovalLayer::where('feature', $feature)
            ->orderBy('level', 'asc');

        if ($kodeDept) {
            // Prioritize department specific rules if they exist, otherwise fallback?
            // For simplicity, let's assume we filter by dept OR where dept is null
            $query->where(function ($q) use ($kodeDept) {
                $q->where('kode_dept', $kodeDept)
                  ->orWhereNull('kode_dept');
            });
        } else {
            $query->whereNull('kode_dept');
        }

        return $query->get();
    }

    /**
     * Check if a user can approve the current step.
     */
    public function canApprove($feature, $currentLevel, $userRole, $kodeDept = null)
    {
        // 1. Get the rule for the next level (currentLevel + 1 usually, or just currentLevel if we track 'last approved')
        // Let's assume currentLevel is "level that has been approved". So we look for currentLevel + 1.
        
        $nextLevel = $currentLevel;
        
        $rule = ApprovalLayer::where('feature', $feature)
            ->where('level', $nextLevel)
            ->where(function ($q) use ($kodeDept) {
                $q->where('kode_dept', $kodeDept)
                  ->orWhereNull('kode_dept');
            })
            ->first();

        if (!$rule) {
            return false; // No more levels
        }

        // Check compatibility (e.g. role matching)
        // Note: Real implementation might need mapping 'hrd' string to actual Role ID or name check
        return $userRole === $rule->role_name;
    }
}
