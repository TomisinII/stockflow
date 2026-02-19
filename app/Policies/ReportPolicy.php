<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    // All three roles can view reports per the seeder
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_reports');
    }

    // Only Admin and Manager can export
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export_reports');
    }
}