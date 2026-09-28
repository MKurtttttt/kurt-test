<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISO Document Status Update</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #374151; margin: 0; padding: 0; background-color: #f3f4f6; }
        .email-container { max-width: 650px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border-top: 6px solid {{ $document->status === 'approved' ? '#10b981' : '#f59e0b' }}; }
        
        .email-header { background: #ffffff; padding: 28px 24px 20px; text-align: center; border-bottom: 1px solid #e5e7eb; }
        .email-header h1 { margin: 0; font-size: 22px; color: #111827; letter-spacing: -0.025em; }
        .email-header p { margin: 6px 0 0; font-size: 13px; color: #6b7280; font-weight: 500; }
        
        .email-body { padding: 28px 36px; }
        
        /* Notice Alert Banner */
        .notice-banner { border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .notice-banner--partial { background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .notice-banner--onhold { background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .notice-title { font-weight: 700; font-size: 15px; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .notice-desc { font-size: 13px; line-height: 1.5; opacity: 0.95; }

        /* Progress Card */
        .progress-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px 20px; margin-bottom: 24px; }
        .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13px; font-weight: 600; color: #374151; }
        .progress-bar-bg { width: 100%; height: 10px; background-color: #e5e7eb; border-radius: 9999px; overflow: hidden; margin-bottom: 8px; }
        .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 9999px; transition: width 0.4s ease; }
        .progress-stats { display: flex; gap: 16px; font-size: 12px; color: #6b7280; }
        .stat-item { display: inline-flex; align-items: center; gap: 4px; }
        .stat-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .stat-dot--approved { background-color: #10b981; }
        .stat-dot--onhold { background-color: #ef4444; }
        .stat-dot--pending { background-color: #f59e0b; }

        /* Document Highlight Card */
        .doc-highlight { background-color: #ffffff; border: 2px solid {{ $document->status === 'approved' ? '#d1fae5' : '#fee2e2' }}; border-radius: 10px; padding: 18px 20px; margin-bottom: 26px; }
        .doc-highlight-label { font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; color: {{ $document->status === 'approved' ? '#059669' : '#dc2626' }}; margin-bottom: 6px; }
        .doc-code { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .doc-title { font-size: 14px; color: #4b5563; margin-bottom: 12px; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.03em; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-on_hold { background-color: #fee2e2; color: #991b1b; }
        .status-submitted_to_idc { background-color: #dbeafe; color: #1e40af; }
        .status-with_qmr { background-color: #e0e7ff; color: #3730a3; }
        .status-pending { background-color: #fef3c7; color: #92400e; }

        /* Table */
        .table-title { font-size: 13px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; }
        .doc-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 24px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .doc-table th { background-color: #f9fafb; text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
        .doc-table td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .doc-table tr:last-child td { border-bottom: none; }
        .doc-table tr.current-doc { background-color: #f0fdf4; font-weight: 600; }

        /* Info Grid */
        .info-box { border-top: 1px solid #e5e7eb; padding-top: 18px; margin-top: 16px; }
        .info-row { display: flex; margin-bottom: 8px; font-size: 13px; }
        .info-label { font-weight: 600; color: #6b7280; width: 150px; flex-shrink: 0; }
        .info-value { color: #111827; }

        .footer { padding: 24px; text-align: center; font-size: 12px; color: #9ca3af; background: #fafafa; border-top: 1px solid #f3f4f6; }
        .footer strong { color: #6b7280; }

        @media only screen and (max-width: 480px) {
            .email-body { padding: 20px; }
            .info-row { flex-direction: column; }
            .info-label { width: 100%; margin-bottom: 2px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>ISO Document Review Update</h1>
            <p>Ticket #{{ $ticket->ticket_number }} &bull; {{ $ticket->originating_section }}</p>
        </div>
        
        <div class="email-body">
            @if ($document->status === 'approved')
                <div class="notice-banner notice-banner--partial">
                    <div class="notice-title">
                        <span>&#10003;</span> Partial Approval Notification
                    </div>
                    <div class="notice-desc">
                        A document in your bulk submission has been reviewed and <strong>approved</strong>. The remaining documents in this ticket are still undergoing review by the IDC team.
                    </div>
                </div>
            @else
                <div class="notice-banner notice-banner--onhold">
                    <div class="notice-title">
                        <span>&#9888;</span> Document Placed On-Hold
                    </div>
                    <div class="notice-desc">
                        A document in your submission has been placed <strong>on-hold</strong> by the IDC team. Please review the ticket details or wait for notes from IDC regarding required adjustments.
                    </div>
                </div>
            @endif

            {{-- Progress Card --}}
            <div class="progress-card">
                <div class="progress-header">
                    <span>Bulk Submission Progress</span>
                    <span>{{ $approvedCount }} of {{ $totalCount }} Approved ({{ $progressPercentage }}%)</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $progressPercentage }}%;"></div>
                </div>
                <div class="progress-stats">
                    <span class="stat-item"><span class="stat-dot stat-dot--approved"></span> {{ $approvedCount }} Approved</span>
                    @if ($onHoldCount > 0)
                        <span class="stat-item"><span class="stat-dot stat-dot--onhold"></span> {{ $onHoldCount }} On-Hold</span>
                    @endif
                    <span class="stat-item"><span class="stat-dot stat-dot--pending"></span> {{ $underReviewCount }} Under Review</span>
                </div>
            </div>

            {{-- Highlighted Document Card --}}
            <div class="doc-highlight">
                <div class="doc-highlight-label">
                    {{ $document->status === 'approved' ? 'Newly Approved Document' : 'Document Placed On-Hold' }}
                </div>
                <div class="doc-code">{{ $document->document_code }}</div>
                <div class="doc-title">{{ $document->document_title }}</div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <span class="status-badge status-{{ $document->status }}">
                        {{ str_replace('_', ' ', $document->status) }}
                    </span>
                    <span style="font-size: 12px; color: #6b7280; text-transform: capitalize;">
                        Modification: <strong>{{ $document->classification }}</strong>
                    </span>
                </div>
            </div>

            {{-- Full Documents Table --}}
            <div class="table-title">All Documents in this Submission ({{ $totalCount }})</div>
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
                        <tr class="{{ $doc->id === $document->id ? 'current-doc' : '' }}">
                            <td>
                                <strong>{{ $doc->document_code }}</strong>
                                @if ($doc->id === $document->id)
                                    <span style="font-size: 10px; color: #059669; font-weight: bold;">(Updated)</span>
                                @endif
                            </td>
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

            {{-- Ticket Meta Info --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Ticket ID:</span>
                    <span class="info-value">#{{ $ticket->ticket_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Originating Dept.:</span>
                    <span class="info-value">{{ $ticket->originating_section }}</span>
                </div>
                @if ($changedBy)
                    <div class="info-row">
                        <span class="info-label">Reviewed By:</span>
                        <span class="info-value">{{ $changedBy }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Timestamp:</span>
                    <span class="info-value">{{ now()->format('M j, Y — h:i A') }}</span>
                </div>
            </div>

            <p style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 28px;">
                This is an automated notification from the Holy Angel University ISO Document Management System.
            </p>
        </div>

        <div class="footer">
            <p><strong>Holy Angel University</strong><br>
            Office of Institutional Effectiveness</p>
            <p>&copy; {{ date('Y') }} HAU-OIE. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
