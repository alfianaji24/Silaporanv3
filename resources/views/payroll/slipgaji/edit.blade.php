<form action="{{ route('slipgaji.update', Crypt::encrypt($slipgaji->kode_slip_gaji)) }}" method="POST" id="formSlipGajiEdit">
    @csrf
    @method('PUT')
    <div class="row g-3">
        {{-- Bulan --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
            <select name="bulan" id="bulan_edit" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach ($list_bulan as $d)
                    <option value="{{ $d['kode_bulan'] }}" {{ $slipgaji->bulan == $d['kode_bulan'] ? 'selected' : '' }}>{{ $d['nama_bulan'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tahun --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
            <select name="tahun" id="tahun_edit" class="form-select">
                <option value="">Pilih Tahun</option>
                @for ($t = $start_year; $t <= date('Y'); $t++)
                    <option value="{{ $t }}" {{ $slipgaji->tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endfor
            </select>
        </div>

        {{-- Kelompok Periode Gaji --}}
        <div class="col-12">
            <label class="form-label fw-bold">Kelompok Periode Cut-Off (Opsional)</label>
            <select name="kode_periode_gaji" id="kode_periode_gaji_edit" class="form-select">
                <option value="">- Tanpa Periode (Default Global) -</option>
                @foreach ($periode_gaji as $p)
                    <option value="{{ $p->kode_periode_gaji }}" {{ $slipgaji->kode_periode_gaji == $p->kode_periode_gaji ? 'selected' : '' }}>
                        {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }}-{{ $p->tanggal_sampai }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted d-block mt-1">Pilih jika ingin mempublish khusus kelompok karyawan periode tertentu.</small>
        </div>

        {{-- Status Penerbitan --}}
        <div class="col-12">
            <label class="form-label fw-bold">Status Penerbitan <span class="text-danger">*</span></label>
            <select name="status" id="status_edit" class="form-select">
                <option value="1" {{ $slipgaji->status == 1 ? 'selected' : '' }}>Publish (Dapat Dilihat Karyawan)</option>
                <option value="0" {{ $slipgaji->status == 0 ? 'selected' : '' }}>Pending (Draft Internal)</option>
            </select>
        </div>

        {{-- Submit Button --}}
        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary w-100" id="btnSimpan">
                <i class="ti ti-send me-1"></i> Update Slip Gaji
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        const form = $('#formSlipGajiEdit');

        function buttonDisable() {
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading..
         `);
        }

        form.submit(function(e) {
            let bulan = form.find('#bulan_edit').val();
            let tahun = form.find('#tahun_edit').val();
            let status = form.find('#status_edit').val();
            
            if (bulan == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Bulan Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#bulan_edit").focus();
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
                        form.find("#tahun_edit").focus();
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
                        form.find("#status_edit").focus();
                    },
                });
                return false;
            }

            buttonDisable();
        });
    });
</script>
