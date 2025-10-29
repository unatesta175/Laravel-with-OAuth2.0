<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Booking #{{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background: #f5f5f5;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff0a85;
        }

        .header h1 {
            color: #ff0a85;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header img {
            margin: 0 auto;
            display: block;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .receipt-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .receipt-info-left,
        .receipt-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .receipt-info-right {
            text-align: right;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .details-section {
            margin: 30px 0;
        }

        .section-title {
            font-size: 18px;
            color: #ff0a85;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .details-table td {
            padding: 12px 0;
        }

        .details-table td:first-child {
            font-weight: 600;
            color: #666;
            width: 200px;
        }

        .amount-section {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }

        .total-row {
            border-top: 2px solid #ff0a85;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #ff0a85;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
            color: #999;
            font-size: 12px;
        }

        .thank-you {
            text-align: center;
            margin: 30px 0;
            font-size: 18px;
            color: #ff0a85;
        }

        .print-button-container {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: white;
        }

        .print-button,
        .download-button {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            background: #ff0a85;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .print-button:hover,
        .download-button:hover {
            background: #d10870;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                padding: 20px;
            }

            .print-button-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print/Download Buttons -->
    <div class="print-button-container">
        <button onclick="downloadAsPDF()" class="download-button">
            📄 Save as PDF
        </button>
    </div>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <!-- Logo -->
            <div style="margin-bottom: 20px;">
                <img src="http://localhost:3000/Logo/big-logo.png" alt="Lunara Spa" style="max-width: 200px; height: auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <h1 style="display: none;">Lunara Spa</h1>
            </div>
            <p>Premium Wellness & Beauty Services</p>
            <p>📍 123 Spa Street, Kuala Lumpur, Malaysia | 📞 +60 12-345 6789 | ✉️ info@lunaraspa.com</p>
        </div>

        <!-- Receipt Info -->
        <div class="receipt-info">
            <div class="receipt-info-left">
                <div class="info-label">Receipt Number</div>
                <div class="info-value">#RCPT-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>

                <div class="info-label">Booking ID</div>
                <div class="info-value">#{{ $booking->id }}</div>

                <div class="info-label">Client Name</div>
                <div class="info-value">{{ $booking->client->name ?? 'N/A' }}</div>

                <div class="info-label">Email</div>
                <div class="info-value">{{ $booking->client->email ?? 'N/A' }}</div>
            </div>

            <div class="receipt-info-right">
                <div class="info-label">Receipt Date</div>
                <div class="info-value">{{ now()->format('F d, Y') }}</div>

                <div class="info-label">Payment Date</div>
                <div class="info-value">{{ $booking->payment->paid_at ? \Carbon\Carbon::parse($booking->payment->paid_at)->format('F d, Y') : 'N/A' }}</div>

                <div class="info-label">Payment Status</div>
                <div class="info-value">
                    <span class="status-badge status-paid">{{ strtoupper($booking->payment->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Service Details -->
        <div class="details-section">
            <h2 class="section-title">Service Details</h2>
            <table class="details-table">
                <tr>
                    <td>Service Name</td>
                    <td>{{ $booking->service->name }}</td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>{{ $booking->service->category->name }}</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>{{ $booking->service->description }}</td>
                </tr>
                <tr>
                    <td>Duration</td>
                    <td>{{ $booking->service->duration }} minutes</td>
                </tr>
                <tr>
                    <td>Therapist</td>
                    <td>{{ $booking->therapist->name }}</td>
                </tr>
            </table>
        </div>

        <!-- Appointment Details -->
        <div class="details-section">
            <h2 class="section-title">Appointment Details</h2>
            <table class="details-table">
                <tr>
                    <td>Appointment Date</td>
                    <td>{{ \Carbon\Carbon::parse($booking->appointment_date)->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td>Appointment Time</td>
                    <td>{{ \Carbon\Carbon::parse($booking->appointment_time, 'UTC')->format('h:i A') }}</td>
                </tr>
                <tr>
                    <td>Booking Status</td>
                    <td>
                        <span class="status-badge status-confirmed">{{ strtoupper(str_replace('_', ' ', $booking->status)) }}</span>
                    </td>
                </tr>
                @if($booking->notes)
                <tr>
                    <td>Notes</td>
                    <td>{{ $booking->notes }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Payment Details -->
        <div class="details-section">
            <h2 class="section-title">Payment Details</h2>
            <table class="details-table">
                <tr>
                    <td>Payment Method</td>
                    <td>{{ $booking->payment->payment_method === 'toyyibpay' ? 'ToyyibPay (Online Payment)' : 'Cash' }}</td>
                </tr>
                @if($booking->payment->toyyibpay_transaction_id)
                <tr>
                    <td>Transaction ID</td>
                    <td>{{ $booking->payment->toyyibpay_transaction_id }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-row">
                <span>Service Amount:</span>
                <span>RM {{ number_format($booking->total_amount, 2) }}</span>
            </div>
            <div class="amount-row">
                <span>Tax (0%):</span>
                <span>RM 0.00</span>
            </div>
            <div class="amount-row total-row">
                <span>Total Paid:</span>
                <span>RM {{ number_format($booking->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            <strong>Thank you for choosing Lunara Spa!</strong>
            <p style="font-size: 14px; color: #666; margin-top: 10px;">
                We look forward to serving you on {{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>For any queries, please contact us at info@lunaraspa.com or call +60 12-345 6789</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>

    <script>
        // Function to download receipt as PDF using browser's print to PDF feature
        function downloadAsPDF() {
            // The user will use browser's "Save as PDF" option in the print dialog
            window.print();
        }

        // Add keyboard shortcut for printing (Ctrl+P or Cmd+P)
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
                event.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>

