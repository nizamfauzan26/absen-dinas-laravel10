<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'lokasi_masuk',
        'lokasi_pulang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const STATUS = [
        'hadir' => 'Hadir',
        'terlambat' => 'Terlambat',
        'dinas_luar' => 'Dinas Luar',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getBadgeAttribute(): string
    {
        return [
            'hadir' => 'bg-green-100 text-green-700',
            'terlambat' => 'bg-amber-100 text-amber-700',
            'dinas_luar' => 'bg-blue-100 text-blue-700',
            'izin' => 'bg-purple-100 text-purple-700',
            'sakit' => 'bg-red-100 text-red-700',
        ][$this->status] ?? 'bg-slate-100 text-slate-700';
    }

    /** Ambil jam (HH:MM) dari kolom time, atau "-" bila kosong. */
    public function jam(string $field): string
    {
        return $this->{$field} ? substr($this->{$field}, 0, 5) : '-';
    }
}
