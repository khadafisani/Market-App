<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'members_id',
        'total',
    ];

    public function members()
    {
        return $this->belongsTo(Member::class, 'members_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function productOut()
    {
        return $this->hasMany(ProductOut::class);
    }
}
