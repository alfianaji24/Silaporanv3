<form action="{{ route('approvallayer.store') }}" method="POST" id="formCreate">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="feature" class="form-label">Feature</label>
                <select name="feature" id="feature" class="form-select" required>
                    <option value="">Pilih Feature</option>
                    @foreach ($features as $f)
                        <option value="{{ $f->feature }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-3">
                <label for="level" class="form-label">Level</label>
                <input type="number" name="level" id="level" class="form-control" placeholder="1" required>
            </div>

            <div class="form-group mb-3">
                <label for="role_name" class="form-label">Role Approver</label>
                <select name="role_name" id="role_name" class="form-select" required>
                    <option value="">Pilih Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="kode_dept" class="form-label">Departemen (Optional)</label>
                <select name="kode_dept" id="kode_dept" class="form-select">
                    <option value="">ALL DEPARTEMEN</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="kode_cabang" class="form-label">Cabang (Optional)</label>
                <select name="kode_cabang" id="kode_cabang" class="form-select">
                    <option value="">ALL CABANG</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="ti ti-send me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>
