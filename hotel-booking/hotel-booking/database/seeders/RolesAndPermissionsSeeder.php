<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Hotels
            'view hotels', 'create hotels', 'edit hotels', 'delete hotels',
        
            // Rooms
            'view rooms', 'create rooms', 'edit rooms',
        
            // Bookings
            'view bookings', 'create bookings', 'edit bookings',
            // Availability
            'view availability', 'update availability',
        
            // Assigned hotel specific
            'view assigned hotels',
        
            // Admin management
            'manage admins',
        ];
        

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to existing roles
        $admin = Role::where('name', 'master')->first();
        $manager = Role::where('name', 'admin')->first();

        if ($admin) {
            $admin->syncPermissions([
                 // Hotels
            'view hotels', 'create hotels', 'edit hotels', 'delete hotels',
        
            // Rooms
            'view rooms', 'create rooms', 'edit rooms',
        
            // Bookings
            'view bookings', 'create bookings', 'edit bookings',
            // Availability
            'view availability', 'update availability',
           // Admin management
            'manage admins',
            ]);
            
        }

        if ($manager) {
            $manager->syncPermissions([
                'view assigned hotels',
                'view rooms', 'create rooms', 'edit rooms',
                'view bookings', 'edit bookings',
                'view availability', 'update availability'
            ]);
            
        }
    }
}
