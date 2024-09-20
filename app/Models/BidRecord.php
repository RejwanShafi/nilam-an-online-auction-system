<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidRecord extends Model
{
    use HasFactory;

    protected $fillable = ['auction_id', 'customer_id', 'amount'];

    public function auctionItem()
    {
        return $this->belongsTo(AuctionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}