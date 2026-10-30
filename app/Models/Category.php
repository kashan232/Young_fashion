<?php

namespace App\Models;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'show_on_website', 'web_image'];

    public function subcategory()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
