<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Disease Reports Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        th, td { border: 1px solid #333; padding: 6px 4px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Disease Reports</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Farmer Name</th>
                <th>Disease</th>
                <th>Assigned Technician</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Date Reported</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $report->farmer_name }}</td>
                    <td>
                        {{ optional($report->technicianReports)->diagnosis_disease ? optional($report->technicianReports)->diagnosis_disease : optional($report->farmerReports)->disease }}
                    </td>
                    <td>{{ optional($report->technicians)->full_name ?? 'N/A' }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->remarks ?? 'N/A' }}</td>
                    <td>
                        {{ $report->created_at instanceof \Carbon\Carbon ? $report->created_at->format('M d, Y H:i A') : $report->created_at }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
