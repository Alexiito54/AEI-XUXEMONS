<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mochila extends Model
{
    protected $table = 'mochila';

    protected $fillable = [
        'user_id',
        'item_id',
        'cantidad',
        'slot',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
