<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $email): User
    {
        $roleModel = Role::firstOrCreate(['name' => $role]);

        return User::create([
            'name' => $role,
            'email' => $email,
            'password' => bcrypt('password'),
            'status' => 'active',
            'role_id' => $roleModel->id,
        ]);
    }

    private function admin(): User
    {
        $admin = $this->account('admin', 'admin@example.test');
        $permission = Permission::firstOrCreate(['name' => 'manage_users']);
        $admin->role->permissions()->sync([$permission->id]);

        return $admin;
    }

    public function test_staff_has_downgrade_and_delete_actions(): void
    {
        $admin = $this->admin();
        $this->account('staff', 'staff@example.test');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Hạ xuống khách hàng')
            ->assertSee('data-role="customer"', false)
            ->assertSee('data-status="deleted"', false);
    }

    public function test_admin_can_promote_and_downgrade_but_cannot_change_admin_role(): void
    {
        $admin = $this->admin();
        Role::firstOrCreate(['name' => 'staff']);
        $customer = $this->account('customer', 'customer@example.test');

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.user.updateRole'), ['user_id' => $customer->id, 'role' => 'staff'])
            ->assertOk();
        $this->assertSame('staff', $customer->fresh()->role->name);

        $this->postJson(route('admin.user.updateRole'), ['user_id' => $customer->id, 'role' => 'customer'])
            ->assertOk();
        $this->assertSame('customer', $customer->fresh()->role->name);

        $this->actingAs($customer, 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.user.updateRole'), ['user_id' => $admin->id, 'role' => 'staff'])
            ->assertForbidden();
        $this->assertSame('admin', $admin->fresh()->role->name);
    }

    public function test_deleted_staff_loses_admin_access_and_can_be_restored(): void
    {
        $admin = $this->admin();
        $staff = $this->account('staff', 'staff@example.test');

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.user.updateStatus'), ['user_id' => $staff->id, 'status' => 'deleted'])
            ->assertOk();
        $this->assertSame('deleted', $staff->fresh()->status);

        $this->actingAs($staff, 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.user.updateStatus'), ['user_id' => $staff->id, 'status' => 'active'])
            ->assertOk();
        $this->assertSame('active', $staff->fresh()->status);
    }
}
