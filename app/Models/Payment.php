<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'track_id',
        'payment_id',
        'ad_id',
        'amount',
        'currency',
        'status',
        'raw_response',
        'payment_from',
        'payment_type',
        'user_id',
        'type'
    ];

    protected $casts = [
        'raw_response' => 'array', 
    ];

    public $appends = ['order_number','url_invoice','name','barCode'];

    public function getCreatedAtAttribute($value){
        return date('Y/m/d', strtotime($value));
    }
    

    public function getOrderNumberAttribute()
    {
        return 'INV-'. (int)$this->amount. '-' . '000'. $this->id;
    }


    public function getNameAttribute()
    {
        return 'Electronic Invoice / فاتورة الكترونية - '. $this->created_at;
        // return $this->track_id .'-'. $this->created_at .'-'. $this->amount ;
    }

 
    public function ad()
    {
        return $this->belongsTo(Ad::class,'ad_id');
    }

    public function payment_type()
    {
        return $this->belongsTo(ItemsCategories::class, 'payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrlInvoiceAttribute()
    {
        return url(app()->getLocale().'/invoice/'.$this->track_id);
    }


    public function getBarCodeAttribute()
{
    $sellerName  = $this->user->name ?? 'اسم البائع';
    $vatNumber   = $this->user->vat_number ?? '123456789000003';
    $invoiceDate = \Carbon\Carbon::parse($this->created_at)->toIso8601String();
    $totalAmount = $this->amount ?? '0.00';
    $vatAmount   = number_format($this->amount * 0.15, 2, '.', '');

    $tlvs = [
        [1, $sellerName],
        [2, $vatNumber],
        [3, $invoiceDate],
        [4, $totalAmount],
        [5, $vatAmount],
    ];

    $qrString = '';
    foreach ($tlvs as $tlv) {
        // حساب الطول بشكل صحيح لدعم UTF-8
        $value   = $tlv[1];
        $length  = strlen($value); // إذا واجهت مشكلة استبدلها بـ mb_strlen($value, '8bit')
        $qrString .= chr($tlv[0]) . chr($length) . $value;
    }

    $qrBase64 = base64_encode($qrString);
    
    // إرجاعه كنص جاهز ليتم عرضه في Vue باستخدام v-html
    return (string) \QrCode::format('svg')->size(200)->generate($qrBase64);
}

}
