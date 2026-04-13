@extends('layouts.app')

@section('title', 'Riwayat Pesan WhatsApp')

@section('content')
@section('navigasi')
<div class="d-flex justify-content-between align-items-center w-100">
    <div>
        Riwayat Pesan WhatsApp
        <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
            Manajemen dan monitoring riwayat pengiriman pesan WhatsApp, melihat status berhasil atau gagal terkirim.
        </div>
    </div>
    <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
        <ol class="breadcrumb breadcrumb-style1 mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.index') }}">
                    <i class="ti ti-home-2 ti-xs"></i>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">
                    <i class="ti ti-device-mobile ti-xs me-1"></i> WhatsApp Gateway
                </a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-message-2 ti-xs me-1"></i> Riwayat Pesan
            </li>
        </ol>
    </nav>
</div>
@endsection
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title mb-0">Riwayat Pesan WhatsApp</h5>
                            <small class="text-muted">
                                Mode: 
                                @if($filter === 'success')
                                    Berhasil Terkirim
                                @elseif($filter === 'failed')
                                    Gagal Terkirim
                                @elseif($filter === 'pending')
                                    Menunggu Diproses
                                @else
                                    Semua Pesan
                                @endif
                            </small>
                        </div>
                        <div class="card-tools d-flex gap-2">
                            <a href="{{ route('wagateway.messages', ['filter' => 'all']) }}" class="btn btn-sm btn-secondary {{ $filter === 'all' ? 'active' : '' }}">
                                Semua Pesan
                            </a>
                            <a href="{{ route('wagateway.messages', ['filter' => 'failed']) }}" class="btn btn-sm btn-danger {{ $filter === 'failed' ? 'active' : '' }}">
                                Gagal Terkirim
                            </a>
                            <a href="{{ route('wagateway.messages', ['filter' => 'pending']) }}" class="btn btn-sm btn-warning {{ $filter === 'pending' ? 'active' : '' }}">
                                Menunggu
                            </a>
                            <a href="{{ route('wagateway.messages', ['filter' => 'success']) }}" class="btn btn-sm btn-success {{ $filter === 'success' ? 'active' : '' }}">
                                Berhasil Terkirim
                            </a>
                            <a href="{{ route('wagateway.index') }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-arrow-left"></i> Kembali ke WA Gateway
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="messagesTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pengirim</th>
                                        <th>Penerima</th>
                                        <th>Pesan</th>
                                        <th>Status</th>
                                        @if($filter !== 'success' && $filter !== 'pending')
                                            <th>Percobaan</th>
                                            <th>Message ID</th>
                                            <th>Error</th>
                                            <th>Aksi</th>
                                        @endif
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $index => $message)
                                        <tr>
                                            <td>{{ $messages->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $message->pengirim }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $message->penerima }}</span>
                                            </td>
                                            <td>
                                                <div class="message-content" style="max-width: 200px;">
                                                    @if (strlen($message->pesan) > 50)
                                                        <span class="message-preview">{{ substr($message->pesan, 0, 50) }}...</span>
                                                        <button class="btn btn-sm btn-link p-0 ms-1" onclick="showFullMessage(this)"
                                                            data-pengirim="{{ $message->pengirim }}"
                                                            data-penerima="{{ $message->penerima }}"
                                                            data-pesan="{{ $message->pesan }}"
                                                            data-status="{{ $message->status }}{{ $message->permanent_failed ? ' (permanent)' : '' }}"
                                                            data-messageid="{{ $message->message_id }}"
                                                            data-error="{{ $message->error_message }}"
                                                            data-tanggal="{{ $message->created_at->format('d/m/Y H:i:s') }}"
                                                            data-attempts="{{ $message->attempts ?? 0 }}"
                                                        >
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    @else
                                                        {{ $message->pesan }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($message->status === 'success')
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-check-circle"></i> Berhasil
                                                    </span>
                                                @elseif ($message->status === 'failed')
                                                    @if ($message->permanent_failed)
                                                        <span class="badge bg-danger">
                                                            <i class="ti ti-alert-circle"></i> Gagal Permanen
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="ti ti-alert-circle"></i> Gagal
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="ti ti-clock"></i> Menunggu
                                                    </span>
                                                @endif
                                            </td>
                                            @if($filter !== 'success' && $filter !== 'pending')
                                                <td>
                                                    {{ $message->attempts ?? 0 }}
                                                </td>
                                                <td>
                                                    @if ($message->message_id)
                                                        <code class="text-success">{{ $message->message_id }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($message->error_message)
                                                        <span class="text-danger" style="max-width: 150px; display: inline-block;">
                                                            {{ substr($message->error_message, 0, 30) }}...
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($message->status === 'failed')
                                                        <form action="{{ route('wagateway.messages.resend', $message->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning">Kirim Ulang</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <small class="text-muted">
                                                    {{ $message->created_at->format('d/m/Y H:i:s') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ ($filter === 'success' || $filter === 'pending') ? 6 : 10 }}" class="text-center">
                                                @if($filter === 'success')
                                                    Belum ada pesan yang berhasil dikirim
                                                @elseif($filter === 'failed')
                                                    Belum ada pesan yang gagal dikirim
                                                @elseif($filter === 'pending')
                                                    Belum ada pesan yang menunggu diproses
                                                @else
                                                    Belum ada pesan yang dikirim
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan pesan lengkap -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Detail Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Pengirim:</strong><br>
                            <span class="badge bg-info" id="modalPengirim"></span>
                        </div>
                        <div class="col-6">
                            <strong>Penerima:</strong><br>
                            <span class="badge bg-secondary" id="modalPenerima"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Status:</strong><br>
                            <span id="modalStatus"></span>
                        </div>
                        <div class="col-6">
                            <strong>Percobaan:</strong><br>
                            <span id="modalAttempts"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Pesan:</strong><br>
                        <div class="border p-3 rounded bg-light" id="modalPesan"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Tanggal:</strong><br>
                            <span id="modalTanggal"></span>
                        </div>
                    </div>
                    <div class="mb-3" id="messageIdSection" style="display: none;">
                        <strong>Message ID:</strong><br>
                        <code id="modalMessageId"></code>
                    </div>
                    <div class="mb-3" id="errorSection" style="display: none;">
                        <strong>Error:</strong><br>
                        <div class="border p-3 rounded bg-danger text-white" id="modalError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        function showFullMessage(button) {
            const pengirim = button.dataset.pengirim || '';
            const penerima = button.dataset.penerima || '';
            const pesan = button.dataset.pesan || '';
            const status = button.dataset.status || '';
            const messageIdValue = button.dataset.messageid || '';
            const error = button.dataset.error || '';
            const tanggal = button.dataset.tanggal || '';
            const attempts = button.dataset.attempts || '0';

            document.getElementById('modalPengirim').textContent = pengirim;
            document.getElementById('modalPenerima').textContent = penerima;
            document.getElementById('modalPesan').textContent = pesan;
            document.getElementById('modalStatus').textContent = status;
            document.getElementById('modalAttempts').textContent = attempts;
            document.getElementById('modalTanggal').textContent = tanggal;

            if (messageIdValue && messageIdValue !== '-') {
                document.getElementById('modalMessageId').textContent = messageIdValue;
                document.getElementById('messageIdSection').style.display = 'block';
            } else {
                document.getElementById('messageIdSection').style.display = 'none';
            }

            if (error && error !== '-') {
                document.getElementById('modalError').textContent = error;
                document.getElementById('errorSection').style.display = 'block';
            } else {
                document.getElementById('errorSection').style.display = 'none';
            }

            $('#messageModal').modal('show');
        }
    </script>
@endpush
