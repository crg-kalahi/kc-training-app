<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class ListUsersWithPermissions extends Command
{
    protected $signature = 'permissions:list-users';
    protected $description = 'List users with their permissions';

    public function handle()
    {
        // Fetch all users with their associated permissions
        $users = User::with('permissions')->get();

        if ($users->isEmpty()) {
            $this->info('No users found');
            return 0;
        }

        foreach ($users as $user) {
            $permissions = $user->permissions->pluck('name')->toArray();

            // Only output users with permissions
            if (!empty($permissions)) {
                $this->info("User: {$user->name} - Permissions: " . implode(', ', $permissions));
            }
        }

        return 0;
    }
}
