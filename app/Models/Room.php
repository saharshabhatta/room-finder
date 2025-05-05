<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Room extends Model{
    protected $fillable = [
        'user_id',
        'description',
        'location',
        'district',
        'province',
        'rent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_rooms');
    }


}


