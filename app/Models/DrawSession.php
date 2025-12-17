<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrawSession extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'number_of_winners'];

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }
}
