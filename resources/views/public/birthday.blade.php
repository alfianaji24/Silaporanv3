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
                <div class="col-12">
                    <h1 class="mb-0 text-center">
                        <i class="bi bi-cake2 me-2"></i>
                        Employee Birthday Data
                    </h1>
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
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karyawan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_karyawan }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->tanggal_lahir)) }}</td>
                            <td>
                                <span class="badge badge-age">{{ $item->umur }} Tahun</span>
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
        function exportToExcel() {
            const table = document.getElementById('birthdayTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    // Remove HTML tags and get text content
                    let text = cols[j].innerText || cols[j].textContent;
                    // Clean up text and escape quotes
                    text = text.replace(/"/g, '""').trim();
                    // Add quotes if contains comma or quote
                    if (text.includes(',') || text.includes('"')) {
                        text = `"${text}"`;
                    }
                    row.push(text);
                }
                csv.push(row.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `data_karyawan_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
        
        // Update timestamp every second
        setInterval(updateTimestamp, 1000);
        
        // Initial update
        updateTimestamp();
    </script>
</body>
</html>
