<!DOCTYPE html>
<html>
<head>
    <title>Daftar Transaksi Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 100px;
            height: auto;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .address {
            font-size: 12px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logobps1.png') }}" class="logo">
        <div class="title">BADAN PUSAT STATISTIK</div>
        <div class="title">KOTA MALANG</div>
        <div class="address">
            Jl. Janti Bar. No.47, Bandungrejosari, Kec. Sukun, Kota Malang, Jawa Timur 65148<br>
            Telp: (0341) 801164, Email: bps3573@bps.go.id
        </div>
    </div>

    <h2 style="text-align: center;">Daftar Transaksi Masuk</h2>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pegawai</th>
                <th>Total</th>
                <th>Detail Barang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('d-m-Y', strtotime($transaction->date)) }}</td>
                <td>{{ $transaction->employee->name }}</td>
                <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                <td>
                    @foreach($transaction->inTransactionDetails as $detail)
                        - {{ $detail->product->name }} ({{ $detail->qty }} {{ $detail->unit }}) - Rp {{ number_format($detail->amount, 0, ',', '.') }}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Total Keseluruhan:</strong></td>
                <td colspan="2"><strong>Rp {{ number_format($transactions->sum('total'), 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>
            Malang, {{ date('d F Y') }}<br>
            Kepala BPS Kota Malang<br><br><br><br>
            ______________________<br>
            NIP.
        </p>
    </div>
</body>
</html>