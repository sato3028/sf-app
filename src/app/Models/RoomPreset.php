<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'host_characters',
        'host_rank',
        'host_mr',
        'requested_characters',
        'min_rank',
        'max_rank',
        'min_mr',
        'max_mr',
        'attributes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
