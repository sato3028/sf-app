<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'host_id', 'host_username', 'host_rank', 'host_mr', 'host_characters', 'requested_characters', 'min_rank', 'max_rank', 'min_mr', 'max_mr', 'status'
    ];

    // 参加者リレーション
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    // チャットリレーション
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    // ホストユーザーリレーション
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(RoomAttribute::class, 'room_attribute_assignments', 'room_id', 'attribute_id');
    }

}
