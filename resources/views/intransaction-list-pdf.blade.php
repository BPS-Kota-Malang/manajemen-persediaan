<!DOCTYPE html>
<html>
<head>
    <title>Daftar Transaksi Masuk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }
        .logo-cell {
            width: 120px; /* Sesuaikan dengan lebar logo */
        }
        .logo {
            width: 100px;
            height: auto;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        .title-italic {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            font-style: italic;
        }
        .address {
            font-size: 12px;
            margin: 5px 0;
            line-height: 1.4;
        }
        .divider {
            border-top: 2px solid #000;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .content-table th {
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
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logobps1.png') }}" class="logo">
            </td>
            <td style="padding-left: 10px;">
                <div class="title-italic">BADAN PUSAT STATISTIK</div>
                <div class="title-italic">KOTA MALANG</div>
                <div class="address">
                    Jl. Janti Bar. No.47, Bandungrejosari, Kec. Sukun, Kota Malang, Jawa Timur 65148<br>
                    Telp: (0341) 801164, Email: bps3573@bps.go.id
                </div>
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <h2 style="text-align: center;">Daftar Transaksi Masuk</h2>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>

    <table class="content-table">
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
