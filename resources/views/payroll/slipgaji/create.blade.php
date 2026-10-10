<form action="{{ route('slipgaji.store') }}" method="POST" id="formSlipGaji">
    @csrf
    <div class="row g-3">
        {{-- Bulan --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
            <select name="bulan" id="bulan" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach ($list_bulan as $d)
                    <option value="{{ $d['kode_bulan'] }}" {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }}>{{ $d['nama_bulan'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tahun --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
            <select name="tahun" id="tahun_modal" class="form-select">
                <option value="">Pilih Tahun</option>
                @for ($t = $start_year; $t <= date('Y'); $t++)
                    <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endfor
            </select>
        </div>

        {{-- Kelompok Periode Gaji --}}
        <div class="col-12">
            <label class="form-label fw-bold">Kelompok Periode Cut-Off (Opsional)</label>
            <select name="kode_periode_gaji" id="kode_periode_gaji" class="form-select">
                <option value="">- Tanpa Periode (Default Global) -</option>
                @foreach ($periode_gaji as $p)
                    <option value="{{ $p->kode_periode_gaji }}">
                        {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }}-{{ $p->tanggal_sampai }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted d-block mt-1">Pilih jika ingin mempublish khusus kelompok karyawan periode tertentu.</small>
        </div>

        {{-- Status Penerbitan --}}
        <div class="col-12">
            <label class="form-label fw-bold">Status Penerbitan <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select">
                <option value="1" selected>Publish (Dapat Dilihat Karyawan)</option>
                <option value="0">Pending (Draft Internal)</option>
            </select>
        </div>

        {{-- Submit Button --}}
        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary w-100" id="submitButtonSlipGaji">
                <i class="ti ti-send me-1"></i> Buat Slip Gaji
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        const form = $('#formSlipGaji');
        form.submit(function(e) {
            let bulan = form.find('#bulan').val();
            let tahun = form.find('#tahun_modal').val();
            let status = form.find('#status').val();
            if (bulan == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Bulan Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#bulan").focus();
                    },
                });
                return false;
            } else if (tahun == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tahun Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#tahun_modal").focus();
                    },
                });
                return false;
            } else if (status == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Status Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#status").focus();
                    },
                });
                return false;
            }
        });
    });
</script>
