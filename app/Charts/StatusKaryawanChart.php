<?php

namespace App\Charts;

use App\Models\Karyawan;
use App\Models\Statuskaryawan;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class StatusKaryawanChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($request = null): \ArielMejiaDev\LarapexCharts\PieChart
    {
        // Ambil data status karyawan dari database
        $statusKaryawanList = Statuskaryawan::orderBy('nama_status_karyawan')->get();
        
        // Ambil jumlah karyawan berdasarkan status
        $query = Karyawan::query();
        $query->where(function($query) {
            $query->where('status_aktif_karyawan', 1)
                  ->orWhere('status_karyawan', 'A'); // Special case: include ASN even if inactive
        });
        $query->select('status_karyawan', DB::raw('count(*) as total'));
        $query->groupBy('status_karyawan');
        
        // Filter berdasarkan akses user jika ada di request
        if (!empty($request->user_cabangs) && is_array($request->user_cabangs)) {
            $query->whereIn('karyawan.kode_cabang', $request->user_cabangs);
        } elseif (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->user_departemens) && is_array($request->user_departemens)) {
            $query->whereIn('karyawan.kode_dept', $request->user_departemens);
        } elseif (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $rawData = $query->pluck('total', 'status_karyawan')->toArray();

        // Konversi kode status ke label lengkap berdasarkan database
        $labels = [];
        $data = [];

        foreach ($statusKaryawanList as $status) {
            $labels[] = $status->nama_status_karyawan;
            $data[] = (int) ($rawData[$status->kode_status_karyawan] ?? 0); // Jika tidak ada data, set 0
        }
        // Generate warna dinamis berdasarkan jumlah data
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];
        $chartColors = array_slice($colors, 0, count($labels));

        return $this->chart->pieChart()
            // ->setTitle('Data Karyawan.')
            // ->setSubtitle('Berdasarkan Status Karyawan')
            ->addData($data)
            ->setLabels($labels)
            ->setColors($chartColors)
            ->setDataLabels(true)
            ->setOptions([
                'dataLabels' => [
                    'enabled' => true,
                    'formatter' => function ($val, $opts) {
                        return round($val, 1) . '%'; // Menampilkan dalam persen
                    },
                    'dropShadow' => [
                        'enabled' => true
                    ]
                ]
            ]);
    }
}
