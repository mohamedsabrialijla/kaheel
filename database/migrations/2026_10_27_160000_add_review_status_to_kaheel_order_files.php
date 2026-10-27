<?php

use App\Models\KaheelOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_order_files') && !Schema::hasColumn('kaheel_order_files', 'review_status')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->string('review_status', 20)->nullable();
            });
        }

        if (!class_exists(KaheelOrder::class) || !Schema::hasColumn('kaheel_order_files', 'review_status')) {
            return;
        }

        KaheelOrder::query()->with('files')->each(function (KaheelOrder $order) {
            $this->stamp($order->files->where('kind', 'receipt')->sortBy('id')->values(), $order->payment_status);
            $this->stamp($order->files->where('kind', 'final_receipt')->sortBy('id')->values(), $order->final_payment_status);
        });
    }

    private function stamp($files, ?string $current): void
    {
        if ($files->isEmpty()) {
            return;
        }
        $lastId = (int) $files->last()->id;
        $current = in_array($current, ['pending', 'approved', 'rejected'], true) ? $current : 'pending';
        foreach ($files as $file) {
            $file->review_status = (int) $file->id === $lastId ? $current : 'rejected';
            $file->save();
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kaheel_order_files', 'review_status')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->dropColumn('review_status');
            });
        }
    }
};
