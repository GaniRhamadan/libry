@extends('rental-hub::layout')

@section('title', 'Reservasi ' . $booking->booking_code)
@section('page-title', 'Detail Reservasi')

@section('content')
<div class="max-w-4xl space-y-6" x-data="{ 
    cancelModal: false, 
    rejectModal: false, 
    returnModal: false,
    manualFee: '{{ $projectedLateFee }}'
}">
    <!-- Header Back Navigation & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $booking->booking_code }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $booking->status->badgeClass() }}">
                    {{ $booking->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Dibuat pada {{ $booking->created_at->translatedFormat(config('rental-hub.date_format', 'd M Y H:i')) }}</p>
        </div>
        <div>
            <a href="{{ route('rental.bookings.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors min-h-[44px]">
                &larr; {{ __('rental-hub::rental.common_back') }}
            </a>
        </div>
    </div>

    <!-- Customer / Admin Status Guidance Banner -->
    @php
        $isAdmin = (auth()->user()->rental_role ?? 'customer') === 'admin';
    @endphp

    @if (!$isAdmin)
        <div class="rounded-2xl border p-5 sm:p-6 {{ $booking->status->badgeClass() }} bg-opacity-10">
            <div class="flex items-start gap-3">
                <div class="mt-0.5">
                    @if ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::PENDING)
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::APPROVED)
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::ACTIVE)
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::RETURNED)
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    @else
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        @if ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::PENDING)
                            Permohonan Sewa Sedang Menunggu Persetujuan
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::APPROVED)
                            Sewa Disetujui — Siap Pengambilan Kunci
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::ACTIVE)
                            Masa Sewa Sedang Berjalan
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::RETURNED)
                            Sewa Telah Selesai
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::REJECTED)
                            Permohonan Sewa Ditolak
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::CANCELLED)
                            Reservasi Dibatalkan
                        @endif
                    </h3>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        @if ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::PENDING)
                            Permohonan sewa Anda sedang menunggu verifikasi oleh pihak admin rental. Anda dapat membatalkan pesanan ini jika ada perubahan jadwal.
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::APPROVED)
                            Selamat! Permohonan Anda disetujui. Silakan datang ke kantor rental pada jadwal pengambilan dengan membawa kartu identitas (KTP/SIM) untuk serah terima armada.
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::ACTIVE)
                            Armada saat ini berada dalam penggunaan Anda. Mohon menjaga kondisi armada dan mengembalikannya sebelum batas waktu untuk menghindari denda keterlambatan.
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::RETURNED)
                            Armada telah berhasil dikembalikan dalam kondisi baik. Terima kasih telah mempercayakan perjalanan Anda kepada kami!
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::REJECTED)
                            Mohon maaf, permohonan sewa tidak dapat diproses oleh pengelola rental.
                        @elseif ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::CANCELLED)
                            Reservasi ini telah dibatalkan. Anda dapat membuat pesanan sewa baru kapan saja melalui katalog armada.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Unit Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Armada Kendaraan</h2>
            
            <div class="flex items-center gap-4">
                <img src="{{ $booking->unit->photo_url }}" 
                     alt="{{ $booking->unit->name }}" 
                     class="w-24 h-16 object-cover rounded-xl bg-slate-100 border border-slate-100 flex-shrink-0">
                <div>
                    <div class="text-xs font-semibold text-indigo-600">{{ $booking->unit->category->name ?? 'Mobil' }}</div>
                    <div class="text-base font-bold text-slate-900">{{ $booking->unit->name }}</div>
                    <div class="text-xs font-mono text-slate-500">{{ $booking->unit->code }}</div>
                </div>
            </div>

            @if (!empty($booking->unit->specifications))
                <div class="pt-2 flex flex-wrap gap-1.5">
                    @foreach ($booking->unit->specifications as $k => $v)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs bg-slate-100 text-slate-700">
                            {{ $k }}: {{ $v }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Customer Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Penyewa</h2>

            <div class="space-y-2.5 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Nama Lengkap:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->user->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Alamat Email:</span>
                    <span class="font-medium text-slate-700">{{ $booking->user->email ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Nomor Telepon:</span>
                    <span class="font-medium text-slate-700">{{ $booking->user->phone ?? 'Tidak dicantumkan' }}</span>
                </div>
            </div>
        </div>

        <!-- Schedule & Duration Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Jadwal & Waktu Sewa</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Mulai:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->formatted_start_time }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Selesai (Rencana):</span>
                    <span class="font-semibold text-slate-900">{{ $booking->formatted_end_time }}</span>
                </div>
                @if ($booking->actual_return_time)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu Kembali (Aktual):</span>
                        <span class="font-semibold text-emerald-700">{{ $booking->formatted_actual_return_time }}</span>
                    </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-slate-100">
                    <span class="text-slate-500">Total Durasi:</span>
                    <span class="font-bold text-slate-900">{{ $booking->total_hours }} Jam</span>
                </div>
            </div>
        </div>

        <!-- Payment & Fees Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Rincian Pembayaran</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tarif Pokok Sewa:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->formatted_base_price }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Denda Keterlambatan:</span>
                    <span class="font-semibold {{ $booking->late_fee > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $booking->formatted_late_fee }}
                    </span>
                </div>

                @if ($booking->status === \RentalHub\StarterKit\Enums\BookingStatus::ACTIVE && $booking->is_overdue)
                    <div class="bg-rose-50 text-rose-800 p-2.5 rounded-xl text-xs flex justify-between items-center">
                        <span>Estimasi denda saat ini ({{ $booking->overdue_hours }} jam):</span>
                        <span class="font-bold">{{ config('rental-hub.currency_symbol', 'Rp ') }}{{ number_format($projectedLateFee, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between pt-3 border-t border-slate-200">
                    <span class="font-bold text-slate-900 text-base">Total Tagihan:</span>
                    <span class="font-bold text-indigo-600 text-base">{{ $booking->formatted_total_price }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes & Reason (If any) -->
    @if ($booking->notes || $booking->cancellation_reason)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
            @if ($booking->notes)
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Catatan Pelanggan:</span>
                    <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-xl">{{ $booking->notes }}</p>
                </div>
            @endif

            @if ($booking->cancellation_reason)
                <div class="pt-2">
                    <span class="text-xs font-bold text-rose-500 uppercase tracking-wider block mb-1">Alasan Pembatalan / Penolakan:</span>
                    <p class="text-sm text-rose-800 bg-rose-50 p-3 rounded-xl border border-rose-100">{{ $booking->cancellation_reason }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Workflow Action Buttons -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="text-xs text-slate-500">
            Aksi yang tersedia disesuaikan dengan izin peran akun dan status reservasi saat ini.
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @can('cancel', $booking)
                <button type="button" 
                        @click="cancelModal = true"
                        class="px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold hover:bg-rose-100 transition-colors min-h-[44px]">
                    {{ __('rental-hub::rental.common_cancel') }} Booking
                </button>
            @endcan

            @can('approve', $booking)
                <form method="POST" action="{{ route('rental.bookings.approve', $booking) }}">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 shadow-sm transition-colors min-h-[44px]">
                        Setujui Reservasi
                    </button>
                </form>
            @endcan

            @can('reject', $booking)
                <button type="button" 
                        @click="rejectModal = true"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors min-h-[44px]">
                    Tolak Reservasi
                </button>
            @endcan

            @can('activate', $booking)
                <form method="POST" action="{{ route('rental.bookings.activate', $booking) }}" onsubmit="return confirm('Konfirmasi penyerahan armada kepada pelanggan?');">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 shadow-sm transition-colors min-h-[44px]">
                        Tandai Armada Diambil (Mulai Sewa)
                    </button>
                </form>
            @endcan

            @can('return', $booking)
                <button type="button" 
                        @click="returnModal = true"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 shadow-sm transition-colors min-h-[44px]">
                    Konfirmasi Pengembalian Armada
                </button>
            @endcan
        </div>
    </div>

    <!-- Modal Cancel -->
    <div x-show="cancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancelModal = false"></div>
            <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
                <h3 class="text-base font-bold text-slate-900 mb-2">Batalkan Reservasi Booking</h3>
                <p class="text-xs text-slate-500 mb-4">Apakah Anda yakin ingin membatalkan booking ini?</p>

                <form method="POST" action="{{ route('rental.bookings.cancel', $booking) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancel_reason" class="block text-xs font-semibold text-slate-700">Alasan Pembatalan (Opsional)</label>
                        <textarea id="cancel_reason" name="cancellation_reason" rows="2" class="mt-1 w-full rounded-xl border border-slate-300 p-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="cancelModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold">Tutup</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700">Ya, Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="rejectModal = false"></div>
            <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
                <h3 class="text-base font-bold text-slate-900 mb-2">Tolak Permohonan Booking</h3>
                <p class="text-xs text-slate-500 mb-4">Tuliskan keterangan penolakan yang akan dapat dilihat oleh pelanggan.</p>

                <form method="POST" action="{{ route('rental.bookings.reject', $booking) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reject_reason" class="block text-xs font-semibold text-slate-700">Alasan Penolakan</label>
                        <textarea id="reject_reason" name="cancellation_reason" rows="3" required placeholder="Contoh: Jadwal armada bentrok dengan pemeliharaan rutin..." class="mt-1 w-full rounded-xl border border-slate-300 p-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700">Konfirmasi Penolakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Return Confirmation -->
    <div x-show="returnModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="returnModal = false"></div>
            <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-xl">
                <h3 class="text-base font-bold text-slate-900 mb-2">Konfirmasi Pengembalian Armada</h3>
                <p class="text-xs text-slate-500 mb-4">
                    Sistem telah mengalkulasi denda otomatis jika pengembalian terlambat. Anda dapat mengubah jumlah denda secara manual jika diperlukan.
                </p>

                <form method="POST" action="{{ route('rental.bookings.return', $booking) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="manual_late_fee" class="block text-xs font-semibold text-slate-700">Jumlah Denda Keterlambatan (Rp)</label>
                        <input type="number" 
                               id="manual_late_fee" 
                               name="manual_late_fee" 
                               x-model="manualFee"
                               min="0"
                               class="mt-1 w-full rounded-xl border border-slate-300 p-2.5 text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-400 mt-1">Dihitung otomatis: {{ config('rental-hub.currency_symbol', 'Rp ') }}{{ number_format($projectedLateFee, 0, ',', '.') }}</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="returnModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 shadow-sm">
                            Konfirmasi Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
