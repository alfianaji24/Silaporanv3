<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Lembur {{ $lembur->nik }}</title>
    <style>
        @page {
            margin: 28mm 12mm 8mm 12mm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 9.5pt;
            line-height: 1.25;
            color: #000000;
        }
        /* Kop Surat Styles */
        header {
            position: fixed;
            top: -24mm;
            left: 0px;
            right: 0px;
            height: 22mm;
            border-bottom: 2px double #000;
            padding-bottom: 2px;
            margin-bottom: 0px;
        }
        .kop-surat table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-logo {
            width: 70px;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo img {
            max-width: 65px;
            max-height: 18mm;
            height: auto;
            display: block;
        }
        .kop-text {
            text-align: left;
            vertical-align: middle;
            padding-left: 8px;
        }
        .kop-text h2 {
            margin: 0 0 1px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .kop-text .branch {
            margin: 0 0 1px 0;
            font-size: 9.5pt;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }
        .kop-text p {
            margin: 0;
            font-size: 8pt;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.15;
            color: #444;
        }
        .content {
            margin-top: 5px;
        }
        table {
            border-collapse: collapse;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    @if(isset($setting))
    <header>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td class="kop-logo">
                    @php
                        $base64 = '';
                        if (!empty($setting->logo)) {
                            // Try storage path first
                            $path = storage_path('app/public/logo/' . $setting->logo);
                            if (!file_exists($path)) {
                                $path = public_path('storage/logo/' . $setting->logo);
                            }
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        }
                    @endphp
                    @if($base64)
                        <img src="{{ $base64 }}" alt="Logo">
                    @endif
                </td>
                <td class="kop-text">
                    <h2>{{ $setting->nama_perusahaan ?? $setting->nama_aplikasi ?? 'PERUSAHAAN' }}</h2>
                    <div class="branch">{{ $lembur->nama_cabang }}</div>
                    <p>{{ $lembur->alamat_cabang ?? $setting->alamat ?? '' }}</p>
                    <p>
                        @if(!empty($lembur->telepon_cabang)) Telp : {{ $lembur->telepon_cabang }} @elseif(!empty($setting->telepon)) Telp : {{ $setting->telepon }} @endif
                    </p>
                </td>
            </tr>
        </table>
    </header>
    @endif

    <div class="content">
        {!! $konten !!}
    </div>
</body>
</html>
