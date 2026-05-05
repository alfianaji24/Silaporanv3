<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Birthday Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .main-content {
            flex: 1;
            padding: 0;
        }
        
        .table-container {
            background: white;
            margin: 0;
            padding: 0;
            min-height: calc(100vh - 200px);
        }
        
        .table-responsive {
            margin: 0;
        }
        
        .table {
            margin: 0;
            font-size: 0.9rem;
        }
        
        .table thead {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .badge-age {
            background-color: #6c757d;
            font-size: 0.8rem;
        }
        
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .today-birthday {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }
        
        .soon-birthday {
            background-color: #e7f3ff !important;
        }
        
        /* Pagination styling */
        .pagination {
            margin: 0;
        }
        
        .pagination .page-link {
            color: #6c757d;
            border-color: #dee2e6;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .pagination .page-link:hover {
            color: #495057;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        
        /* Hide Laravel default pagination info */
        .pagination-container .pagination-info {
            display: none !important;
        }
        
        /* Hide Laravel default pagination info text */
        .pagination .d-flex > div:first-child {
            display: none !important;
        }
        
        .pagination .page-item.active .page-link:hover {
            background-color: #495057;
            border-color: #495057;
            color: white;
        }
        
        /* Hide Laravel default pagination info */
        .pagination-container .d-flex:last-child {
            display: none;
        }
        
        .pagination-container .d-flex:first-child {
            justify-content: flex-end !important;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-section {
                padding: 1rem 0;
            }
            
            .header-section h1 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .table th, .table td {
                padding: 0.5rem;
                vertical-align: middle;
            }
            
            .badge-age {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
            
            .footer {
                font-size: 0.8rem;
            }
        }
        
        /* Desktop Fullscreen */
        @media (min-width: 769px) {
            .main-content {
                overflow-y: auto;
            }
            
            .table-container {
                padding: 1.5rem;
            }
            
            .table {
                font-size: 0.95rem;
            }
            
            .table th, .table td {
                padding: 0.75rem;
            }
        }
        
        /* Print styles */
        @media print {
            body {
                background: white;
            }
            
            .header-section {
                background: none !important;
                color: black !important;
                box-shadow: none;
            }
            
            .footer {
                background: none !important;
                color: black !important;
            }
            
            .table-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="bi bi-cake2 me-2"></i>
                        Employee Birthday
                    </h1>
                </div>
                <div class="col-md-4 text-md-end">
                    <button id="refreshButton" class="btn btn-light btn-sm" onclick="manualRefresh()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover" id="birthdayTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Tanggal Lahir</th>
                            <th>Umur</th>
                            <th>Hari Sampai Ultah</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karyawan as $item)
                        <tr data-days-until="{{ $item->hari_sampai_ultah }}">
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->nama_karyawan }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->tanggal_lahir)) }}</td>
                            <td>
                                <span class="badge badge-age">{{ $item->umur }} Tahun</span>
                            </td>
                            <td>
                                @if($item->hari_sampai_ultah == 0)
                                    <span class="badge bg-danger animate-pulse">Hari Ini!</span>
                                @elseif($item->hari_sampai_ultah == 1)
                                    <span class="badge bg-warning text-dark">Besok</span>
                                @elseif($item->hari_sampai_ultah <= 7)
                                    <span class="badge bg-warning">{{ $item->hari_sampai_ultah }} hari</span>
                                @elseif($item->hari_sampai_ultah <= 30)
                                    <span class="badge bg-primary">{{ $item->hari_sampai_ultah }} hari</span>
                                @else
                                    <span class="badge bg-secondary">{{ $item->hari_sampai_ultah }} hari</span>
                                @endif
                            </td>
                            <td>{{ $item->nama_jabatan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($karyawan->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted mt-3">Tidak ada data karyawan</h4>
                <p class="text-muted">Belum ada data karyawan aktif yang tersedia.</p>
            </div>
            @else
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Info:</strong> Data diperbarui secara real-time. Ulang tahun hari ini ditandai dengan <span class="badge bg-danger">Hari Ini!</span>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted pagination-info">
                        <small>Menampilkan {{ $karyawan->firstItem() }} - {{ $karyawan->lastItem() }} dari total {{ $karyawan->total() }} karyawan</small>
                    </div>
                    <div class="pagination-buttons">
                        {{ $karyawan->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} Silaporan</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="bi bi-clock me-1"></i>
                        Terakhir diperbarui: <span id="lastUpdated">{{ date('d-m-Y H:i:s') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh functionality
        let refreshInterval;
        let isRefreshing = false;
        
        function refreshBirthdayData() {
            if (isRefreshing) return;
            
            isRefreshing = true;
            const refreshButton = document.getElementById('refreshButton');
            if (refreshButton) {
                refreshButton.disabled = true;
                refreshButton.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Memperbarui...';
            }
            
            // Get current page from URL
            const currentUrl = new URL(window.location.href);
            const currentPage = currentUrl.searchParams.get('page') || '1';
            
            // Fetch current page data
            fetch(`${currentUrl.pathname}?page=${currentPage}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('#birthdayTable tbody');
                const currentTableBody = document.querySelector('#birthdayTable tbody');
                const newPagination = doc.querySelector('.d-flex.justify-content-between .d-flex');
                const currentPagination = document.querySelector('.d-flex.justify-content-between .d-flex');
                const newInfo = doc.querySelector('.text-muted small');
                const currentInfo = document.querySelector('.text-muted small');
                
                if (newTableBody && currentTableBody) {
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                    highlightTodayBirthdays();
                    updateTimestamp();
                }
                
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                }
                
                if (newInfo && currentInfo) {
                    currentInfo.textContent = newInfo.textContent;
                }
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
            })
            .finally(() => {
                isRefreshing = false;
                if (refreshButton) {
                    refreshButton.disabled = false;
                    refreshButton.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Refresh';
                }
            });
        }
        
        function highlightTodayBirthdays() {
            const rows = document.querySelectorAll('#birthdayTable tbody tr');
            rows.forEach(row => {
                const daysUntil = parseInt(row.getAttribute('data-days-until'));
                row.classList.remove('today-birthday', 'soon-birthday');
                
                if (daysUntil === 0) {
                    row.classList.add('today-birthday');
                } else if (daysUntil <= 7) {
                    row.classList.add('soon-birthday');
                }
            });
        }
        
        function startAutoRefresh() {
            // Refresh every 60 seconds
            refreshInterval = setInterval(refreshBirthdayData, 60000);
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }
        
        // Manual refresh function
        function manualRefresh() {
            refreshBirthdayData();
        }

        // Real-time timestamp update
        function updateTimestamp() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const formattedTime = `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
            const timestampElement = document.getElementById('lastUpdated');
            
            if (timestampElement) {
                timestampElement.textContent = formattedTime;
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            highlightTodayBirthdays();
            updateTimestamp();
            startAutoRefresh();
            
            // Update timestamp every second
            setInterval(updateTimestamp, 1000);
            
            // Pause auto-refresh when page is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefresh();
                } else {
                    startAutoRefresh();
                    // Refresh immediately when page becomes visible again
                    refreshBirthdayData();
                }
            });
        });
        
        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
        });
    </script>
</body>
</html>
