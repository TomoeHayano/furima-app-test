<?php

namespace Tests\Feature\User;

use App\Models\Category;
use App\Models\ProductCondition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出品フォームで入力した情報が正しく保存される
     */
    public function test_出品フォームの入力が正しく保存される(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categoryA = Category::factory()->create(['name' => '家電']);
        $categoryB = Category::factory()->create(['name' => 'ゲーム']);
        $condition = ProductCondition::factory()->create(['status_name' => '良好']);

        $imagePath = $this->createTemporaryImage();

        $payload = [
            'name' => '新発売カメラ',
            'brand_name' => 'TestBrand',
            'description' => '最新モデルのカメラです。',
            'price' => 30000,
            'condition_id' => $condition->id,
            'category_ids' => [$categoryA->id, $categoryB->id],
            'image' => $imagePath,
        ];

        $response = $this->actingAs($user)->post(route('sell.store'), $payload);

        $response->assertRedirect(route('user.mypage', ['page' => 'sell']));

        $this->assertDatabaseHas('products', [
            'name' => '新発売カメラ',
            'brand_name' => 'TestBrand',
            'description' => '最新モデルのカメラです。',
            'price' => 30000,
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);

        $product = $user->products()->where('name', '新発売カメラ')->first();

        $this->assertNotNull($product);
        $this->assertDatabaseHas('product_category', [
            'product_id' => $product->id,
            'category_id' => $categoryA->id,
        ]);
        $this->assertDatabaseHas('product_category', [
            'product_id' => $product->id,
            'category_id' => $categoryB->id,
        ]);

        Storage::disk('public')->assertExists($product->image_path ? str_replace('/storage/', '', $product->image_path) : '');
    }

    private function createTemporaryImage(): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'img');
        $jpegData = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBIQFhUWFRUVFRUVFRUYFRUVFRUXFhUYHSggGBolHRUVITEhJSkrLi4uFx8zODMsNygtLisBCgoKDg0OGxAQGi0fHyYtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAKgBLAMBIgACEQEDEQH/xAAbAAABBQEBAAAAAAAAAAAAAAAEAAEFAwYCB//EADwQAAIBAwIEBAMGBQMEAwAAAAECAwAEEQUSITEGEyJBUWFxBzKBkaGx8BQjQlJicrLB0RUWQ0NjkqLC8TWi8f/EABsBAAIDAQEBAAAAAAAAAAAAAAQFAgMGAQAH/8QAMBEAAgIBAwIEBQMFAQAAAAAAAAECEQMEEiExQRMiUWFxgZGh8DKBkbHB0fFS/9oADAMBAAIRAxEAPwC1qiICIiAiIgIiICIiAiIgIiICIiAiIgIiICIiAiIgLx1ZsaAJ+lZeI7IuP89xDSHLheXRIlnSRrped1IovnHgkmHGawEq+y3OC/YLXTr4Wr9PXgY7vRl6Vw567L90ij1ZPgimeISFRCGDpa2BkLomqKgJo0vvArkH5AO9M/dwZ0pniS3pSke1Mt2rt7NmBGG99nmCaTKty+PBkO5OVwOB1p5MNDoAuCEi0aKBslZx2drfacr/AKtP1leonX2NDSSSXTHSa14qRcbRKodui6Tn6PvxI6fN5lHppZrmnOSO4SS2SSHqzTeWY5Avzkdxl3pNGdiszIciQ3Uczdlz7SoTTDo1B4ezgmO6ijLJMEVNUEYyvGN28AL5soiceqd7qV3CyMfCVxYvBy06SnVAk0nnk3uKxR271GGB5BPdZiZsaAJ+lZeI7IuP89xDTvlaQt1zGhcXd1LSeEW776BF3nf6/Dr7guP5AnbcW2kwiCIiAiIgIiICIiAiIgIiICIiAiIgIiICIiAiIgL//2Q==');
        file_put_contents($tmpFile, $jpegData);

        return new UploadedFile(
            $tmpFile,
            'camera.jpg',
            'image/jpeg',
            null,
            true
        );
    }
}
