<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeMessagePhoto extends Model
{
    protected $fillable = ['trade_message_id', 'photo_path'];

    public function message()
    {
        return $this->belongsTo(TradeMessage::class, 'trade_message_id');
    }
}
