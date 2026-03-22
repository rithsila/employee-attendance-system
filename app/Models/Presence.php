<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'attendance_id', 'presence_date',
        'presence_enter_time', 'presence_out_time', 'is_permission',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
