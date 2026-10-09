<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'body', 'user_id']; 
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $appends = ['created_at_human'];

    /**
     * علاقة صاحب الإشعار
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة المستخدمين الذين استلموا الإشعار مع حالة القراءة
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('read')
                    ->withTimestamps();
    }

  
    public function currentUser()
{
    return $this->belongsToMany(User::class, 'notification_user')
                ->withPivot('read')
                ->withTimestamps()
                ->where('user_id', auth('sanctum')->id());
}
  

    /**
     * التاريخ بصيغة بشرية حسب لغة التطبيق
     */
    public function getCreatedAtHumanAttribute()
    {
        Carbon::setLocale(App::getLocale());
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * إعادة created_at بصيغة 'Y-m-d H:i' عند الاستدعاء
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }
}
