<form action="{{ route('approvalfeature.update', $feature->id) }}" method="POST" id="formEdit">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="feature" class="form-label">Kode Feature (Unique)</label>
                <input type="text" name="feature" id="feature" class="form-control" value="{{ $feature->feature }}" required oninput="this.value = this.value.toUpperCase()">
            </div>
            
            <div class="form-group mb-3">
                <label for="name" class="form-label">Nama Feature</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $feature->name }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ $feature->description }}</textarea>
            </div>

            <div class="form-group">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="ti ti-send me-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</form>
