<!Doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 0; background-color: #f3f4f6; }
        .email-container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border-top: 6px solid #667eea; }
        
        .email-header { background: #ffffff; padding: 30px 20px; text-align: center; border-bottom: 1px solid #e5e7eb; }
        .email-header h1 { margin: 0; font-size: 22px; color: #111827; letter-spacing: -0.025em; }
        
        .email-body { padding: 32px 40px; }
        .intro-text { text-align: center; margin-bottom: 24px; color: #4b5563; font-size: 16px; }

        /* Enhanced Status Transition */
        .status-timeline { display: flex; align-items: center; justify-content: center; background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #f3f4f6; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 12px; text-transform: uppercase; white-space: nowrap; }
        .status-arrow { color: #9ca3af; margin: 0 15px; font-weight: bold; }

        /* Status Colors */
        .status-pending { background-color: #fef3c7; color: #f0bc12; }
        .status-submitted_to_idc { background-color: #dbeafe; color: #1abdda; }
        .status-with_qmr { background-color: #e0e7ff; color: #3730a3; }
        .status-approved { background-color: #d1fae5; color: #0a9b1d; }
        .status-on_hold { background-color: #fee2e2; color: #991b1b; }

        /* Documents Table */
        .doc-table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .doc-table th { background-color: #f9fafb; text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
        .doc-table td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .doc-table tr:last-child td { border-bottom: none; }
        .all-approved-badge { background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; margin: 18px 0; font-size: 13px; font-weight: 600; text-align: center; }

        /* Info Grid */
        .info-box { margin: 24px 0; border-top: 1px solid #f3f4f6; padding-top: 20px; }
        .info-row { display: flex; margin-bottom: 12px; font-size: 14px; }
        .info-label { font-weight: 600; color: #6b7280; width: 150px; flex-shrink: 0; }
        .info-value { color: #111827; font-weight: 500; }
   
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #9ca3af; }
        .footer strong { color: #6b7280; }

        @media only screen and (max-width: 480px) {
            .email-body { padding: 20px; }
            .info-row { flex-direction: column; }
            .info-label { width: 100%; margin-bottom: 2px; }
            .status-timeline { flex-direction: column; gap: 10px; }
            .status-arrow { transform: rotate(90deg); margin: 5px 0; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>ISO Ticket Update</h1>
        </div>
        
        <div class="email-body">
            <p class="intro-text">The following ticket has been transitioned to a new stage in the <strong>ISO Document Management System</strong>.</p>

            <div class="status-timeline">
                <span class="status-badge status-{{ $oldStatus }}">
                    {{ str_replace('_', ' ', $oldStatus) }}
                </span>
                <span class="status-arrow">→</span>
                <span class="status-badge status-{{ $ticket->status }}">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>

            @if ($ticket->status === 'approved')
                <div class="all-approved-badge">
                    &#10003; All documents in this ticket have been successfully reviewed and approved.
                </div>
            @endif

            @if ($ticket->documents && $ticket->documents->count() > 0)
                <div style="font-size: 13px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 20px; margin-bottom: 8px;">
                    Documents in this Ticket ({{ $ticket->documents->count() }})
                </div>
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ticket->documents as $doc)
                            <tr>
                                <td><strong>{{ $doc->document_code }}</strong></td>
                                <td>{{ $doc->document_title }}</td>
                                <td>
                                    <span class="status-badge status-{{ $doc->status }}">
                                        {{ str_replace('_', ' ', $doc->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Ticket ID:</span>
                    <span class="info-value">#{{ $ticket->ticket_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Originating Dept.:</span>
                    <span class="info-value">{{ $ticket->originating_section }}</span>
                </div>
                @if ($ticket->creator->name)
                    <div class="info-row">
                        <span class="info-label">Ticket Owner:</span>
                        <span class="info-value">{{ $ticket->creator->name}}</span>
                    </div>
                @endif
                @if ($changedBy)
                    <div class="info-row">
                        <span class="info-label">Updated By:</span>
                        <span class="info-value">{{ $changedBy }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Timestamp:</span>
                    <span class="info-value">{{ now()->format('M j, Y — h:i A') }}</span>
                </div>
            </div>

            <p style="text-align: center; color: #9ca3af; font-size: 13px; margin-top: 40px; border-top: 1px solid #f3f4f6; padding-top: 20px;">
                This is an automated system notification. Please do not reply to this email.
            </p>
        </div>
    </div>

    <div class="footer">
        <p><strong>Holy Angel University</strong><br>
        Office of Institutional Effectiveness</p>
        <p>© {{ date('Y') }} HAU-OIE. All rights reserved.</p>
    </div>
</body>
</html>