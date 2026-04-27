<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'service_interested', 
        'source', 'status', 'notes', 'marketer_id'
    ];

    // A lead belongs to the marketer who generated it
    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }
}