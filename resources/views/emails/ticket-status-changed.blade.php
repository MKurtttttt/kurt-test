<!Doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            margin: 5px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-submitted_to_idc { background-color: #dbeafe; color: #1e40af; }
        .status-with_qmr { background-color: #e0e7ff; color: #3730a3; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-on_hold { background-color: #fee2e2; color: #991b1b; }
        .info-box {
            background-color: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 140px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1> ISO Ticket Status Update</h1>
        </div>
        <div class="email-body">
            <p>A ticket status has been updated in the ISO Document Management System.</p>
        </div>
        <!-- Status Change display -->
        <div style="text-align: center; margin: 30px 0;">
            <span class="status-badge status-{{ $oldStatus }}">
                {{ ucwords(str_replace('_', ' ', $oldStatus)) }}
            </span>
            <span style="font-size: 24px; margin: 0 10px;">-></span>
            <span class="status-badge status-{{ $ticket->status }}">
                {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
            </span>
        </div>

        <!-- Ticket Information -->
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Ticket ID:</span>
                <span>{{ $ticket->ticket_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Department/Office:</span>
                <span>{{ $ticket->originating_section }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Submitted by:</span>
                <span>{{ $ticket->creator->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status changed by:</span>
                <span>{{ $changedBy}}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date & Time:</span>
                <span>{{ now()->format('F j, Y - g:i A') }}</span>
            </div>
        </div>

        <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
            This is an automated notification from the ISO Document Management System.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Holy Angel University</strong><br>
        Office of Institutional Effectiveness - IDMO</p>
        <p style="margin-top: 10px;">
            © {{ date('Y') }} HAU-OIE. All rights reserved.
        </p>
    </div>
</body>
</html>