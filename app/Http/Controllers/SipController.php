<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SipController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('karyawan')) {
            $userkaryawan = \App\Models\Userkaryawan::where('id_user', $user->id)->first();
            $sips = Sip::where('nik', $userkaryawan->nik)
                ->orderByDesc('tanggal_akhir')
                ->orderByDesc('created_at')
                ->get();

            return view('datamaster.sip.index_mobile', compact('sips'));
        }

        $query = Sip::select(
            'sip.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'jabatan.nama_jabatan',
            'cabang.nama_cabang',
            'departemen.nama_dept'
        )
            ->leftJoin('karyawan', 'sip.nik', '=', 'karyawan.nik')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'sip.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'sip.kode_dept', '=', 'departemen.kode_dept');

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereIn('sip.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $query->whereIn('sip.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->nama_karyawan) {
            $query->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if ($request->kode_cabang) {
            $query->where('sip.kode_cabang', $request->kode_cabang);
        }

        if ($request->kode_dept) {
            $query->where('sip.kode_dept', $request->kode_dept);
        }

        $sips = $query->orderByDesc('sip.tanggal_akhir')
            ->orderByDesc('sip.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('datamaster.sip.index', [
            'sips' => $sips,
            'filterNama' => $request->nama_karyawan,
            'filterCabang' => $request->kode_cabang,
            'filterDept' => $request->kode_dept,
            'cabangs' => $user->getCabang(),
            'departemens' => $user->getDepartemen(),
        ]);
    }

    public function create()
    {
        return view('datamaster.sip.create', $this->formDependencies());
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['status_sip'] = '1';

        if ($request->hasFile('file_sip')) {
            $data['file_sip'] = $request->file('file_sip')->store('sip', 'public');
        }

        Sip::create($data);
        return Redirect::back()->with(messageSuccess('SIP berhasil disimpan.'));
    }

    public function edit(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);
        $sip = Sip::findOrFail($id);

        if ($sip->status_sip == '0') {
            return Redirect::back()->with(messageError('SIP sudah nonaktif tidak dapat diedit.'));
        }

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($sip->kode_cabang, $userCabangs) || !in_array($sip->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke SIP ini.');
            }
        }

        return view('datamaster.sip.edit', array_merge(
            $this->formDependencies(),
            ['sip' => $sip]
        ));
    }

    public function update(Request $request, string $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $sip = Sip::findOrFail($id);

        if ($sip->status_sip == '0') {
            return Redirect::back()->with(messageError('SIP sudah nonaktif tidak dapat diupdate.'));
        }

        $data = $this->validateRequest($request, $sip->id);

        if ($request->hasFile('file_sip')) {
            if ($sip->file_sip && Storage::disk('public')->exists($sip->file_sip)) {
                Storage::disk('public')->delete($sip->file_sip);
            }
            $data['file_sip'] = $request->file('file_sip')->store('sip', 'public');
        }

        $sip->update($data);
        return Redirect::back()->with(messageSuccess('SIP berhasil diperbarui.'));
    }

    public function destroy(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);
        $sip = Sip::findOrFail($id);

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($sip->kode_cabang, $userCabangs) || !in_array($sip->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke SIP ini.');
            }
        }

        if ($sip->file_sip && Storage::disk('public')->exists($sip->file_sip)) {
            Storage::disk('public')->delete($sip->file_sip);
        }
        $sip->delete();
        return Redirect::back()->with(messageSuccess('SIP berhasil dihapus.'));
    }

    public function downloadFile(string $encryptedId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $id = Crypt::decrypt($encryptedId);
        $sip = Sip::findOrFail($id);

        if (!$sip->file_sip || !Storage::disk('public')->exists($sip->file_sip)) {
            return Redirect::back()->with(messageError('File SIP tidak ditemukan.'));
        }

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            if (!in_array($sip->kode_cabang, $userCabangs) || !in_array($sip->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke file ini.');
            }
        }

        return response()->download(Storage::disk('public')->path($sip->file_sip), 'SIP-' . ($sip->no_sip ?? 'document') . '.pdf');
    }

    protected function formDependencies(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $karyawanQuery = Karyawan::select('nik', 'nama_karyawan', 'kode_dept', 'kode_cabang');

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $karyawanQuery->whereIn('kode_cabang', $userCabangs);
            } else {
                $karyawanQuery->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $karyawanQuery->whereIn('kode_dept', $userDepartemens);
            } else {
                $karyawanQuery->whereRaw('1 = 0');
            }
        }

        return [
            'karyawans' => $karyawanQuery->orderBy('nama_karyawan')->get(),
            'cabangs' => $user->getCabang(),
            'departemens' => $user->getDepartemen(),
        ];
    }

    protected function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $rules = [
            'no_sip' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'exists:karyawan,nik'],
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
            'kode_cabang' => ['required', 'exists:cabang,kode_cabang'],
            'kode_dept' => ['required', 'exists:departemen,kode_dept'],
            'file_sip' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];

        if ($ignoreId) {
            $rules['no_sip'] = ['required', 'string', 'max:100', Rule::unique('sip', 'no_sip')->ignore($ignoreId)];
        } else {
            $rules['no_sip'] = ['required', 'string', 'max:100', 'unique:sip,no_sip'];
        }

        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            $rules['kode_cabang'] = array_merge($rules['kode_cabang'], [Rule::in($userCabangs)]);
            $rules['kode_dept'] = array_merge($rules['kode_dept'], [Rule::in($userDepartemens)]);
        }

        $messages = [
            'kode_cabang.in' => 'Anda tidak memiliki akses ke cabang yang dipilih.',
            'kode_dept.in' => 'Anda tidak memiliki akses ke departemen yang dipilih.',
            'file_sip.mimes' => 'File harus berformat PDF.',
            'file_sip.max' => 'Ukuran file maksimal 5 MB.',
        ];

        $validated = $request->validate($rules, $messages);
        unset($validated['file_sip']);
        return $validated;
    }
}
