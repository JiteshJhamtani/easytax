<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - {{ $applicantName }}</title>
    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        @page {
            margin: 0; 
        }
        @media print {
            body {
                padding: 1.5cm; 
            }
        }
        }
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 20px 40px;
        }

        .brand-header {
            width: 100%;
            border-bottom: 4px solid #1E9C5D; 
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: table;
        }
        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 40%;
        }
        .header-logo img {
            max-height: 55px; 
        }
        .header-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 60%;
        }
        .header-info h1 {
            color: #1E9C5D;
            margin: 0 0 5px 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-info p {
            margin: 2px 0;
            font-size: 13px;
            color: #4b5563;
        }

        .section-header {
            text-align: center;
            margin: 20px 0 15px 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .section-subtitle {
            font-size: 12px;
            color: #4b5563;
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #1E9C5D;
        }
        .table th, .table td {
            border: 1px solid #d1d5db;
            padding: 7px 10px;
        }
        .table th {
            background-color: #1E9C5D;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        .amount-col {
            text-align: right !important;
            width: 120px;
        }
        .total-row td {
            background-color: #1E9C5D;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            border-color: #16a34a; 
        }
        .group-title {
            font-weight: bold;
            background-color: #f3f4f6;
            color: #1f2937;
            text-transform: uppercase;
            font-size: 10px;
        }
        .font-weight-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="brand-header">
        <div class="header-logo">
<img src="{{ public_path('assets/images/logo.png') }}" alt="EasyTax Logo">        </div>
        <div class="header-info">
            <h1>Financial Statements</h1>
            <p style="font-size: 15px; margin-bottom: 5px;"><strong>M/S {{ $applicantName }}</strong></p>
            <p>PAN: <strong>{{ $panNumber }}</strong></p>
            <p>FY: 2024-25</p>
            <p>AY: 2025-26</p>
        </div>
    </div>

    <div class="section-header">
        <h2 class="section-title">Trading & Profit & Loss A/C</h2>
        <p class="section-subtitle">For The Year Ended on 31st March, 2025</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Particulars</th>
                <th class="amount-col">Amount (₹)</th>
                <th>Particulars</th>
                <th class="amount-col">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>To Opening Stock</td>
                <td class="amount-col">{{ number_format($data['opening_stock'], 2) }}</td>
                <td>By Sales</td>
                <td class="amount-col">{{ number_format($data['sales'], 2) }}</td>
            </tr>
            <tr>
                <td>To Purchases</td>
                <td class="amount-col">{{ number_format($data['purchases'], 2) }}</td>
                <td>By Closing Stock</td>
                <td class="amount-col">{{ number_format($data['closing_stock'], 2) }}</td>
            </tr>
            <tr>
                <td>To Direct Expenses</td>
                <td class="amount-col">{{ number_format($data['direct_expenses'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="font-weight-bold">To Gross Profit c/d</td>
                <td class="amount-col font-weight-bold">{{ number_format($grossProfit, 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td class="amount-col">{{ number_format($tradingTotal, 2) }}</td>
                <td>Total</td>
                <td class="amount-col">{{ number_format($tradingTotal, 2) }}</td>
            </tr>

            <tr>
                <td>To Salary Expenses</td>
                <td class="amount-col">{{ number_format($data['salaries'], 2) }}</td>
                <td class="font-weight-bold">By Gross Profit b/d</td>
                <td class="amount-col font-weight-bold">{{ number_format($grossProfit, 2) }}</td>
            </tr>
            <tr>
                <td>To Electricity Exp.</td>
                <td class="amount-col">{{ number_format($data['electricity'], 2) }}</td>
                <td>By Interest Income</td>
                <td class="amount-col">{{ number_format($data['interest_income'], 2) }}</td>
            </tr>
            <tr>
                <td>To Shop Rent</td>
                <td class="amount-col">{{ number_format($data['shop_rent'], 2) }}</td>
                <td>By Other Income</td>
                <td class="amount-col">{{ number_format($data['other_income'], 2) }}</td>
            </tr>
            <tr>
                <td>To Telephone & Internet</td>
                <td class="amount-col">{{ number_format($data['telephone_internet'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>To Printing & Stationery</td>
                <td class="amount-col">{{ number_format($data['printing_stationery'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>To Repairs & Maintenance</td>
                <td class="amount-col">{{ number_format($data['repairs_maintenance'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>To Interest on Loan</td>
                <td class="amount-col">{{ number_format($data['interest_on_loan'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>To Other Expenses</td>
                <td class="amount-col">{{ number_format($data['other_expenses'], 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="font-weight-bold">To Net Profit</td>
                <td class="amount-col font-weight-bold">{{ number_format($netProfit, 2) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-row">  
                <td>Total</td>
                <td class="amount-col">{{ number_format($pnlTotal, 2) }}</td>
                <td>Total</td>
                <td class="amount-col">{{ number_format($pnlTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="page-break-after: always;"></div>

    <div class="brand-header">
        <div class="header-logo">
          <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo11.png'))) }}" alt="EasyTax Logo">
        </div>
        <div class="header-info">
            <h1>Financial Statements</h1>
            <p><strong>M/S {{ $applicantName }}</strong></p>
            <p>PAN: <strong>{{ $panNumber }}</strong> | FY: 2024-25 | AY: 2025-26</p>
        </div>
    </div>

    <div class="section-header">
        <h2 class="section-title">Balance Sheet</h2>
        <p class="section-subtitle">As on 31st March, 2025</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Liabilities</th>
                <th class="amount-col">Amount (₹)</th>
                <th>Assets</th>
                <th class="amount-col">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="group-title">CAPITAL ACCOUNT</td>
                <td class="group-title"></td>
                <td class="group-title">FIXED ASSETS</td>
                <td class="group-title"></td>
            </tr>
            <tr>
                <td>Capital Account</td>
                <td class="amount-col">{{ number_format($closingCapital, 2) }}</td>
                <td>Furniture</td>
                <td class="amount-col">{{ number_format($data['furniture'], 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Computer</td>
                <td class="amount-col">{{ number_format($data['computer'], 2) }}</td>
            </tr>
            <tr>
                <td class="group-title">LONG TERM LIABILITIES</td>
                <td class="group-title"></td>
                <td>Vehicle</td>
                <td class="amount-col">{{ number_format($data['vehicle'], 2) }}</td>
            </tr>
            <tr>
                <td>Loan taken from Bank</td>
                <td class="amount-col">{{ number_format($data['bank_loan'], 2) }}</td>
                <td>Other Fixed Assets</td>
                <td class="amount-col">{{ number_format($data['other_fixed_assets'], 2) }}</td>
            </tr>
            <tr>
                <td>Other Loans</td>
                <td class="amount-col">{{ number_format($data['other_loans'], 2) }}</td>
                <td class="group-title">INVESTMENTS</td>
                <td class="group-title"></td>
            </tr>
            <tr>
                <td class="group-title">CURRENT LIABILITIES</td>
                <td class="group-title"></td>
                <td>Total Investments</td>
                <td class="amount-col">{{ number_format($data['total_investments'], 2) }}</td>
            </tr>
            <tr>
                <td>Sundry Creditors</td>
                <td class="amount-col">{{ number_format($data['sundry_creditors'], 2) }}</td>
                <td class="group-title">CURRENT ASSETS</td>
                <td class="group-title"></td>
            </tr>
            <tr>
                <td>Other Current Liab.</td>
                <td class="amount-col">{{ number_format($data['other_current_liabilities'], 2) }}</td>
                <td>Sundry Debtors</td>
                <td class="amount-col">{{ number_format($data['sundry_debtors'], 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Cash in Hand</td>
                <td class="amount-col">{{ number_format($data['cash_in_hand'], 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Bank Balance</td>
                <td class="amount-col">{{ number_format($data['bank_balance'], 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Closing Stock</td>
                <td class="amount-col">{{ number_format($data['closing_stock'], 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>TDS</td>
                <td class="amount-col">{{ number_format($data['tds'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td class="amount-col">{{ number_format($bsTotal, 2) }}</td>
                <td>Total</td>
                <td class="amount-col">{{ number_format($bsTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">
        <h2 class="section-title">Capital Account</h2>
        <p class="section-subtitle">As on 31/03/2025</p>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Particulars</th>
                <th class="amount-col">Amount (₹)</th>
                <th>Particulars</th>
                <th class="amount-col">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>To Drawings</td>
                <td class="amount-col">{{ number_format($data['drawings'], 2) }}</td>
                <td>By Balance b/d (Opening)</td>
                <td class="amount-col">{{ number_format($data['opening_capital'], 2) }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">To Balance c/d (Closing)</td>
                <td class="amount-col font-weight-bold">{{ number_format($closingCapital, 2) }}</td>
                <td>By Net Profit</td>
                <td class="amount-col">{{ number_format($netProfit, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td class="amount-col">{{ number_format($capitalTotal, 2) }}</td>
                <td>Total</td>
                <td class="amount-col">{{ number_format($capitalTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
 
    
</body>
</html>