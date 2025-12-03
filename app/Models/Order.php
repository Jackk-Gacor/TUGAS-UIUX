<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $total_price
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $note
 * @property string $payment_method
 * @property string $status
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'customer_name',
        'customer_phone',
        'note',
        'payment_method',
        'status',
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
