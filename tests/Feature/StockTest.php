<?php

namespace Tests\Feature;

use App\Models\StockItem;
use Tests\TestCase;

class StockTest extends TestCase
{
    public function test_employee_can_view_stock(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->get('/stock');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_stock_item(): void
    {
        $this->actingAs($this->createAdmin());
        $response = $this->post('/stock', [
            'name' => 'Cotton Fabric',
            'type' => 'raw_material',
            'quantity' => 100,
            'unit' => 'meters',
            'minimum_quantity' => 20,
        ]);
        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseHas('stock_items', ['name' => 'Cotton Fabric']);
    }

    public function test_employee_cannot_create_stock_item(): void
    {
        $this->actingAs($this->createEmployee());
        $response = $this->post('/stock', [
            'name' => 'Test',
            'type' => 'raw_material',
            'quantity' => 10,
            'unit' => 'kg',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_update_stock_item(): void
    {
        $admin = $this->createAdmin();
        $item = StockItem::create([
            'name' => 'Original', 'type' => 'raw_material', 'quantity' => 50,
            'unit' => 'kg', 'minimum_quantity' => 10, 'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $response = $this->put("/stock/{$item->id}", [
            'name' => 'Updated', 'type' => 'raw_material', 'quantity' => 75,
            'unit' => 'kg', 'minimum_quantity' => 10,
        ]);
        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseHas('stock_items', ['id' => $item->id, 'name' => 'Updated']);
    }

    public function test_admin_can_delete_stock_item(): void
    {
        $admin = $this->createAdmin();
        $item = StockItem::create([
            'name' => 'Delete Me', 'type' => 'finished_good', 'quantity' => 10,
            'unit' => 'pieces', 'minimum_quantity' => 5, 'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $response = $this->delete("/stock/{$item->id}");
        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseMissing('stock_items', ['id' => $item->id]);
    }
}
