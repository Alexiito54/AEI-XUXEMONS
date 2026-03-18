<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combate extends Model
{
    protected $fillable = [
        'retador_id', 'rival_id',
        'xuxemon_retador_id', 'xuxemon_rival_id',
        'estado', 'ganador_id'
    ];

    public function retador()
    {
        return $this->belongsTo(User::class, 'retador_id', 'user_id');
    }

    public function rival()
    {
        return $this->belongsTo(User::class, 'rival_id', 'user_id');
    }

    public function xuxemonRetador()
    {
        return $this->belongsTo(Xuxemon::class, 'xuxemon_retador_id');
    }

    public function xuxemonRival()
    {
        return $this->belongsTo(Xuxemon::class, 'xuxemon_rival_id');
    }
}
