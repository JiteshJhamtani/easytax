<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use \App\Traits\MasksSensitiveData, HasFactory;

    protected $maskable = ['email', 'phone', 'amount'];

    protected $fillable = [
        'name', 'email', 'phone', 'service_interested',
        'source', 'status', 'notes', 'marketer_id', 'amount',
    ];

    // A lead belongs to the marketer who generated it
    public function marketer()
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }
}
