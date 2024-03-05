<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;

    protected $fillable =[ 

        'model','description','price','user_id','manufacturer_id','category_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
