<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kode QR Santri</title>
    <style>
        @page {
            margin: 10mm 8mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333333;
            background-color: #ffffff;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid #4f46e5;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            color: #1e1b4b;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            color: #6b7280;
        }
        table.grid {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        table.grid td {
            width: 25%;
            padding: 3px;
            vertical-align: top;
        }
        .card {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 4px;
            text-align: center;
            background-color: #ffffff;
            height: 172px;
            overflow: hidden;
        }
        .card-header {
            font-size: 8px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 4px;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 3px;
        }
        .qr-code img {
            width: 105px;
            height: 105px;
            display: block;
            margin: 0 auto;
        }
        .student-name {
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            margin-top: 5px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    @foreach($students->chunk(16) as $pageStudents)
        <div class="{{ $loop->last ? '' : 'page-break' }}">
            <div class="header">
                <h2>{{ $tpqProfile->name ?? 'TPQ' }}</h2>
                <p>KARTU KODE QR ABSENSI SANTRI</p>
            </div>

            <table class="grid">
                @foreach($pageStudents->chunk(4) as $row)
                    <tr>
                        @foreach($row as $student)
                            <td>
                                <div class="card">
                                    <div class="card-header">
                                        {{ $tpqProfile->name ?? 'TPQ' }}
                                    </div>
                                    <div class="qr-code">
                                        <img src="{{ $student->qr_code }}" alt="QR Code">
                                    </div>
                                    <div class="student-name">
                                        {{ $student->name }}
                                    </div>
                                </div>
                            </td>
                        @endforeach

                        @for($i = $row->count(); $i < 4; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>
</html>