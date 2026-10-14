<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $general_setting->theme_color_1 ?? '#0a5c41' }}">
    <title>Ulang Tahun Pegawai | {{ $general_setting->nama_aplikasi ?? 'Silaporan' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}">
</head>
<body class="relative min-h-screen overflow-x-hidden bg-slate-50">
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="pointer-events-none fixed inset-0 overflow-hidden" style="background: linear-gradient(135deg, {{ $themeColor1 }} 0%, {{ $themeColor2 }} 100%);">
    <img src="{{ asset('assets/login/images/bg-login-v2.jpg') }}"
         alt=""
         aria-hidden="true"
         class="h-full w-full scale-105 object-cover object-center"
         style="opacity: 0.12; -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 35%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,1) 100%); mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 35%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,1) 100%);">
    <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
</div>

<main class="relative z-10 mx-auto max-w-6xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
    <header class="flex flex-col gap-3 rounded-2xl p-4 text-white shadow-xl shadow-slate-950/10 backdrop-blur-md sm:flex-row sm:items-end sm:justify-between sm:p-5"
            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
        <div class="flex items-center gap-3">
            @if (!empty($general_setting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists('logo/' . $general_setting->logo))
                <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="" class="h-12 w-12 rounded-xl object-cover">
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-lg font-black text-white">
                    {{ strtoupper(substr($general_setting->nama_aplikasi ?? 'S', 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-white/75">
                <div class="mt-1 flex items-center gap-2">
                    <h1 class="text-xl font-black tracking-tight text-white sm:text-2xl">Ulang Tahun Pegawai</h1>
                </div>
            </div>
        </div>
        <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-white/25 bg-white/15 px-3.5 py-2 text-xs font-bold text-white shadow-2xs">
            <i class="ti ti-users text-sm"></i>
            {{ $birthdayList->count() }} Pegawai
        </div>
    </header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs">
        <div class="flex items-center justify-between gap-3 px-4 py-3 text-white sm:px-5"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <div class="flex items-center gap-2"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-center">No.</th>
                        <th class="px-4 py-3">Pegawai</th>
                        <th class="px-4 py-3">Tanggal Ulang Tahun</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3 text-center">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($birthdayList as $pegawai)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-center text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $pegawai->nama_karyawan }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-700">
                                {{ $pegawai->next_birthday->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $pegawai->nama_jabatan ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($pegawai->days_until_birthday === 0)
                                    <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700">Hari ini</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                        {{ $pegawai->days_until_birthday }} hari lagi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-14 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="ti ti-cake-off text-2xl"></i>
                                </div>
                                <p class="mt-3 text-sm font-bold text-slate-700">Belum ada data ulang tahun</p>
                                <p class="mt-1 text-xs text-slate-400">Pegawai aktif atau ASN dengan tanggal lahir akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
