<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Slip Gaji Karyawan</h2>
    <p><strong>Nama Karyawan:</strong> {{ $nama_karyawan }}</p>
    <p><strong>Jabatan:</strong> {{ $nama_jabatan }}</p>
    <p><strong>Bulan:</strong> {{ $bulan }} {{ $tahun }}</p>

    <table>
        <thead>
            <tr>
                <th>Absensi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>  
        <tr>
                <td>Hadir</td>
                <td>{{ $kehadiran }} hari</td>
            </tr> 
            <tr>
                <td>Izin</td>
                <td>{{ $izin }} hari</td>
            </tr>
            <tr>
                <td>Cuti</td>
                <td>{{ $cuti }} hari</td>
            </tr>
            <tr>
                <td>Dinas Luar Kota</td>
                <td>{{ $dinas_luar_kota }} hari</td>
            </tr>
           
            <tr>
                <td>Lembur</td>
                <td>{{ $lembur }} jam</td>
            </tr>
            <tr>
    <td><strong>Total Kehadiran</strong></td>
    <td><strong>{{ $total_kehadiran }} hari</strong></td>
</tr>

            


        </tbody>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td>{{ $gaji_pokok }}</td>
            </tr>
            <tr>
            <tr>
            <tr>
    <td>Uang Kehadiran ({{ $total_kehadiran }} hari × {{ $uang_kehadiran_perhari }})</td>
    <td>{{ $uang_kehadiran }}</td>
</tr>

<tr>
                <td>Uang Makan  ({{ $total_kehadiran }} hari × {{ $uang_makan_perhari }})</td>
                <td>Rp {{ number_format($uang_makan, 0, ',', '.') }}</td>
            </tr>


            </tr>
            <tr>
                <td>Tunjangan</td>
                <td>{{ $tunjangan }}</td>
            </tr>
            
            <tr>
                <td>Kehadiran</td>
                <td>{{ $kehadiran }} hari</td>
            </tr>

            
            
            <tr>
    <td>Bonus Lembur ({{ $lembur }} jam × {{ number_format($bonus, 0) }})</td>
    <td>{{ number_format($total_bonus, 0) }}</td>
</tr>

<tr>
                <td>Potongan</td>
                <td>{{ $potongan }}</td>
            </tr>

            <tr>
                <td><strong>Total Gaji</strong></td>
                <td><strong>Rp {{ number_format($gaji_pokok + $tunjangan + $uang_kehadiran + $uang_makan + $total_bonus - $potongan, 0, ',', '.') }}</strong></td>
            </tr>

        </tbody>
    </table>
</body>
</html>
