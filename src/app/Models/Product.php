<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
  use HasFactory;

  public $incrementing = false;
  protected $keyType = 'string';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'name',
    'brand_name',
    'description',
    'price',
    'condition_id',
    'image_path',
    'is_sold',
  ];

  /**
   * Get the user that owns the product.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the product condition.
   */
  public function condition(): BelongsTo
  {
    return $this->belongsTo(ProductCondition::class, 'condition_id');
  }

  /**
   * Get the likes for the product.
   */
  public function likes(): HasMany
  {
    return $this->hasMany(Like::class);
  }

  /**
   * Get the comments for the product.
   */
  public function comments(): HasMany
  {
    return $this->hasMany(Comment::class);
  }

  /**
   * Get the categories for the product.
   */
  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id');
  }

  /**
   * Get the order for the product.
   */
  public function order(): HasOne
  {
    return $this->hasOne(Order::class, 'product_id');
  }

  protected $casts = [
    'is_sold' => 'boolean', // tinyint を true/false として扱う
  ];

  protected static function booted()
  {
    static::creating(function (Product $product) {
      if (! $product->id) {
        $product->id = self::generateProductId();
      }
    });
  }

  protected static function generateProductId(): string
  {
    $latestId = self::orderBy('id', 'desc')->value('id');
    $latest   = $latestId ? (int) substr($latestId, 2) : 0;

    return 'CO' . str_pad((string) ($latest + 1), 2, '0', STR_PAD_LEFT);
  }
}
