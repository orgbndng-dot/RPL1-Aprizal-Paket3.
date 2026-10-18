<?php
// Fungsi bantu untuk fitur laporan (dipakai admin & siswa)
const STATUS_LAPORAN = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

function badge_status(string $status): string {
    $kelas = 'status-' . strtolower($status);
    return '<span class="status ' . $kelas . '">' . htmlspecialchars($status) . '</span>';
}

function tgl_indo(?string $datetime): string {
    if (!$datetime) return '-';
    return date('d-m-Y H:i', strtotime($datetime));
}
