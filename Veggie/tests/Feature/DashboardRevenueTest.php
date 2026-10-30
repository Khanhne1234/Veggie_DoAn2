<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\Order;
use App\Models\Role;
use App\Models\ShippingAddress;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class DashboardRevenueTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_groups_non_canceled_revenue_by_day_week_and_month(): void
    {
        CarbonImmutable::setTestNow('2026-10-29 12:00:00');

        $role = Role::query()->create(['name' => 'customer']);
        $user = User::query()->create([
            'name' => 'Khách hàng',
            'email' => 'revenue@example.test',
            'password' => 'password',
            'status' => 'active',
            'role_id' => $role->id,
        ]);
        $address = ShippingAddress::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Khách hàng',
            'phone' => '0123456789',
            'address' => '1 Đường Xanh',
            'city' => 'Hà Nội',
            'default' => true,
        ]);

        $this->createOrder($user, $address, 100, '2026-10-29 10:00:00');
        $this->createOrder($user, $address, 50, '2026-10-28 10:00:00');
        $this->createOrder($user, $address, 200, '2026-10-19 10:00:00');
        $this->createOrder($user, $address, 300, '2026-09-29 10:00:00');
        $this->createOrder($user, $address, 700, '2025-10-28 10:00:00');
        $this->createOrder($user, $address, 1000, '2026-10-29 09:00:00', 'canceled');

        $controller = app(DashboardController::class);
        $day = $controller->index(Request::create('/admin/dashboard?revenue_period=day'));
        $week = $controller->index(Request::create('/admin/dashboard?revenue_period=week'));
        $month = $controller->index(Request::create('/admin/dashboard?revenue_period=month'));

        $this->assertCount(30, $day->getData()['revenueSeries']['labels']);
        $this->assertSame(350.0, array_sum($day->getData()['revenueSeries']['values']));
        $this->assertCount(12, $week->getData()['revenueSeries']['labels']);
        $this->assertSame(650.0, array_sum($week->getData()['revenueSeries']['values']));
        $this->assertCount(12, $month->getData()['revenueSeries']['labels']);
        $this->assertSame(650.0, array_sum($month->getData()['revenueSeries']['values']));
        $this->assertSame('theo tháng', $month->getData()['revenuePeriodLabel']);
    }

    private function createOrder(User $user, ShippingAddress $address, int $amount, string $createdAt, string $status = 'completed'): void
    {
        $order = Order::query()->create([
            'user_id' => $user->id,
            'shipping_address_id' => $address->id,
            'total_price' => $amount,
            'status' => $status,
        ]);
        $order->created_at = CarbonImmutable::parse($createdAt);
        $order->save();
    }
}
