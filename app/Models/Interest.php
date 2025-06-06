<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Interest extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
