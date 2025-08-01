<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Products extends Model
{
	use HasFactory;
	protected $table = 'products';
	protected $primaryKey = 'product_id';
	public $timestamps = true;

	protected $fillable = ['product_name', 'product_sku', 'product_image', 'product_quantity', 'product_price', 'product_tax'];

	/**
	 * Get all of the variants for the Products
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function variants(): HasMany
	{
		return $this->hasMany(ProductVariants::class, 'variant_product_id', 'product_id');
	}
}
