<form action="{{ route('penjadwalancard.update', Crypt::encrypt($jadwalcard->id)) }}" method="POST" id="formEditJadwalCard">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-semibold">Nama Jadwal / Keterangan Periode <span class="text-danger">*</span></label>
            <input type="text" name="nama_jadwal" class="form-control" value="{{ $jadwalcard->nama_jadwal }}" required>
        </div>

        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold">Dari Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                <input type="text" name="dari" id="dari_edit" class="form-control flatpickr-date-modal-edit" value="{{ $jadwalcard->dari }}" placeholder="YYYY-MM-DD" required>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold">Sampai Tanggal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                <input type="text" name="sampai" id="sampai_edit" class="form-control flatpickr-date-modal-edit" value="{{ $jadwalcard->sampai }}" placeholder="YYYY-MM-DD" required>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold">Cabang (Opsional)</label>
            <select name="kode_cabang" class="form-select select2-modal">
                <option value="">Semua Cabang</option>
                @foreach ($cabang as $c)
                    <option value="{{ $c->kode_cabang }}" {{ $jadwalcard->kode_cabang == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold">Departemen (Opsional)</label>
            <select name="kode_dept" class="form-select select2-modal">
                <option value="">Semua Departemen</option>
                @foreach ($departemen as $d)
                    <option value="{{ $d->kode_dept }}" {{ $jadwalcard->kode_dept == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Catatan</label>
            <textarea name="keterangan" class="form-control" rows="2">{{ $jadwalcard->keterangan }}</textarea>
        </div>

        <div class="col-12 text-end mt-4">
            <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Simpan Perubahan</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.select2-modal').select2({
            dropdownParent: $('#modal')
        });

        $(".flatpickr-date-modal-edit").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>
