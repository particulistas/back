<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'parent_id'];

  /*   public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    } */

    /* public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
 */
 
   /*  public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'category_id');
    } */

      // Relación con categoría padre (si es subcategoría)
      public function parent(): BelongsTo
      {
          return $this->belongsTo(Category::class, 'parent_id');
      }
  
      // Relación con subcategorías
      public function children(): HasMany
      {
          return $this->hasMany(Category::class, 'parent_id');
      }
  
      // Relación con propiedades
      public function properties(): HasMany
      {
          return $this->hasMany(Property::class);
      }

}
