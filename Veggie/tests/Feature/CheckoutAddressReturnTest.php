<?php

namespace Tests\Feature;

use App\Models\ShippingAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventoryFixtures;
use Tests\TestCase;

class CheckoutAddressReturnTest extends TestCase
{
    use RefreshDatabase, BuildsInventoryFixtures;

    public function test_address_created_from_checkout_returns_to_checkout_and_is_selected(): void
    {
        $customer = $this->createInventoryCustomer();
        $this->putInventoryCart($this->createInventoryProduct(), 1, $customer);
        $this->createInventoryAddress($customer);

        $this->actingAs($customer)
            ->get(route('account', ['return_to' => 'checkout']))
            ->assertOk()
            ->assertSee('name="return_to" value="checkout"', false)
            ->assertSee('id="liton_tab_addresses"', false);

        $response = $this->postJson(route('account.address.add'), [
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000000',
            'address' => '123 Duong A',
            'city' => 'Ha Noi',
            'return_to' => 'checkout',
        ])->assertOk()->assertJsonPath('success', true);

        $address = ShippingAddress::where('user_id', $customer->id)->latest('id')->firstOrFail();
        $this->assertSame(route('checkout', ['address_id' => $address->id]), $response->json('redirect_url'));

        $this->get($response->json('redirect_url'))
            ->assertOk()
            ->assertViewHas('defaultAddress', fn ($selected) => $selected->id === $address->id);
    }

    public function test_address_created_from_account_does_not_redirect_to_checkout(): void
    {
        $customer = $this->createInventoryCustomer();

        $this->actingAs($customer)
            ->postJson(route('account.address.add'), [
                'full_name' => 'Nguyen Van B',
                'phone' => '0900000001',
                'address' => '456 Duong B',
                'city' => 'Ha Noi',
            ])
            ->assertOk()
            ->assertJsonPath('redirect_url', null);
    }
}
