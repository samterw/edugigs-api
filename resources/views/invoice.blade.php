<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EduGigs_Official_Receipt_{{ $order->id }}</title>
</head>
<body>

    <div class="top-bar"></div>

    <div class="container">
        <table class="header-table">
            <tr>
                <td class="brand-section">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('public/images/EduGigs.png'))) }}" class="logo-img">
                </td>
                <td class="invoice-meta">
                    <h2 class="invoice-title">Receipt</h2>
                    <div class="mono">REF: #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>

        <table class="details-grid">
            <tr>
                <td class="info-block" style="width: 45%;">
                    <div class="label">Billed To</div>
                    <div class="value">{{ $order->buyer->name }}</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Verified Student ID: {{ $order->buyer_id }}</div>
                </td>
                <td style="width: 10%;"></td>
                <td class="info-block" style="width: 45%;">
                    <div class="label">Issued By</div>
                    <div class="value">{{ $order->gig->user->name }}</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Faculty Provider: {{ $order->gig->user->faculty ?? 'UNIMAS Student' }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Service Specification</th>
                    <th style="text-align: right;">Completion Date</th>
                    <th style="text-align: right;">Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 16px;">{{ $order->gig->title }}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 5px;">
                            Category: {{ $order->gig->category }} | Transaction ID: {{ $order->billplz_id ?? 'FPX-LOCAL' }}
                        </div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;" class="value">
                        {{ $order->updated_at->format('d M Y') }}
                    </td>
                    <td style="text-align: right; vertical-align: middle; font-size: 18px; font-weight: 800;">
                        RM {{ number_format($order->final_price ?: $order->gig->price, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="financial-footer">
            <tr>
                <td class="stamp-container">
                    <div class="paid-stamp">Electronic Verified</div>
                    <div style="margin-top: 15px; font-size: 10px; color: #94a3b8; font-weight: 600;">
                        Payment Status: <strong>SUCCESSFUL</strong><br>
                        Authenticated via EduGigs Protocol
                    </div>
                </td>
                <td style="width: 45%;">
                    <div class="total-card">
                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #1d4ed8; margin-bottom: 5px;">Total Amount Settled</div>
                        <div style="font-size: 36px; font-weight: 900;">RM {{ number_format($order->final_price ?: $order->gig->price, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-terms">
            <strong>Notes & Policies:</strong><br>
            1. This receipt confirms the full settlement of the service listed above.<br>
            2. Services are provided by students of UNIMAS and are governed by the EduGigs Code of Conduct.<br>
            3. For any discrepancies, please contact the EduGigs support team with the reference ID shown above.
        </div>

        <div class="barcode-section">
            <div class="barcode-container">
                <span class="bar" style="width: 4px;"></span><span class="space" style="width: 2px;"></span>
                <span class="bar" style="width: 1px;"></span><span class="space" style="width: 3px;"></span>
                <span class="bar" style="width: 6px;"></span><span class="space" style="width: 2px;"></span>
                <span class="bar" style="width: 2px;"></span><span class="space" style="width: 5px;"></span>
                <span class="bar" style="width: 8px;"></span><span class="space" style="width: 1px;"></span>
                <span class="bar" style="width: 3px;"></span>
            </div>
            <div class="mono">ORD{{ $order->id }}U{{ $order->buyer_id }}G{{ $order->gig_id }}</div>
            <p style="font-size: 8px; color: #cbd5e1; margin-top: 15px; font-weight: 600; text-transform: uppercase;">
                Software-Generated Audit Trail — Universiti Malaysia Sarawak (UNIMAS)
            </p>
        </div>
    </div>

</body>
</html>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap');
        
        @page { margin: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #0f172a;
            line-height: 1.5;
        }

        /* Top Accent Bar */
        .top-bar {
            height: 10px;
            background: linear-gradient(90deg, #1d4ed8, #4f46e5);
            width: 100%;
        }

        .container { padding: 50px; }

        /* Header Layout */
        .header-table { width: 100%; margin-bottom: 60px; }
        .brand-section { width: 60%; vertical-align: middle; }
        
        .logo-img {
            height: 90px; /* Increased from 60px to 90px */
            width: auto;
            display: block; /* Center it better now that it's solo */
        }

        .brand-text {
            display: inline-block;
            vertical-align: middle;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 800;
            color: #1d4ed8;
            letter-spacing: -1.5px;
            line-height: 1;
        }

        .brand-sub {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        .invoice-meta { text-align: right; vertical-align: top; }
        .invoice-title {
            font-size: 36px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
            margin: 0;
            color: #0f172a;
        }

        /* Split Info Layout */
        .details-grid { width: 100%; margin-bottom: 50px; }
        .info-block {
            padding: 25px;
            background-color: #f8fafc;
            border-radius: 20px;
            vertical-align: top;
            border: 1px solid #f1f5f9;
        }

        .label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }

        .value { font-size: 14px; font-weight: 700; color: #1e293b; }

        /* Line Items */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #0f172a;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .items-table td { padding: 25px 15px; border-bottom: 1px solid #e2e8f0; }

        /* UPDATED: Light Theme Total Card */
        .financial-footer { width: 100%; margin-top: 20px; }
        .stamp-container { width: 55%; vertical-align: middle; }
        
        .paid-stamp {
            border: 4px double #10b981;
            color: #10b981;
            display: inline-block;
            padding: 10px 25px;
            font-weight: 800;
            font-size: 20px;
            text-transform: uppercase;
            border-radius: 4px;
            transform: rotate(-5deg);
        }

        .total-card {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 30px;
            border-radius: 24px;
            text-align: right;
            border: 2px solid #1d4ed8; /* Cobalt border to highlight the total */
        }

        /* Barcode Section */
        .barcode-section {
            margin-top: 80px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 50px;
        }

        .barcode-container { display: inline-block; height: 50px; font-size: 0; }
        .bar { display: inline-block; height: 50px; background-color: #0f172a; }
        .space { display: inline-block; height: 50px; background-color: #ffffff; }

        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 12px;
            letter-spacing: 6px;
            font-weight: 500;
        }

        .footer-terms { margin-top: 40px; font-size: 9px; color: #94a3b8; line-height: 1.6; }
    </style>