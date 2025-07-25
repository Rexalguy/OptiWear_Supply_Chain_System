<x-mail::message>
# Supplier's Weekly Report

Hello <strong>{{ $supplier->name }}</strong>,

This is your weekly report for the period ending <strong>{{ $reportDate }}</strong>.

---

<h2 style="color:#2d3748; margin-bottom:10px;">Raw Materials Purchase Summary</h2>
<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; background:#f9f9f9; margin-bottom:20px;">
    <thead>
        <tr style="background:#2d3748; color:#fff;">
            <th align="left" style="border-bottom:2px solid #e2e8f0;">Raw Materials Order Status</th>
            <th align="right" style="border-bottom:2px solid #e2e8f0;">Number of Orders</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="color:#ed8936;">Pending</td>
            <td align="right" style="color:#ed8936;">{{ $pendingOrdersCount }}</td>
        </tr>
        <tr style="background:#edf2f7;">
            <td style="color:#3182ce;">Confirmed</td>
            <td align="right" style="color:#3182ce;">{{ $confirmedOrdersCount }}</td>
        </tr>
        <tr>
            <td style="color:#38a169;">Delivered</td>
            <td align="right" style="color:#38a169;">{{ $deliveredOrdersCount }}</td>
        </tr>
        <tr style="background:#edf2f7;">
            <td style="color:#e53e3e;">Cancelled</td>
            <td align="right" style="color:#e53e3e;">{{ $cancelledOrdersCount }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td align="right"><strong>{{ $totalOrdersCount }}</strong></td>
        </tr>
    </tbody>
</table>

<p style="font-size:16px; color:#2d3748;">
    <strong>Total Sales:</strong> UGX {{ number_format($totalSales, 2) }}
</p>
<p>
    <strong>Note:</strong> These total sales constitute orders whose statuses are either <strong>delivered</strong> or <strong>confirmed</strong>. Please consider visiting your account for more information.
</p>

<p>
    Please review the attached report for full details.
</p>

<x-mail::panel>
    <strong>Note:</strong> This report is automatically generated.<br>
    <ul style="margin-top:8px;">
        <li>For any discrepancies, please reach out to our support team.</li>
        <li>Ensure to check the status of your orders regularly.</li>
        <li>Keep track of your inventory levels to avoid stockouts.</li>
        <li>We appreciate your continued partnership and look forward to serving you.</li>
    </ul>
</x-mail::panel>

<x-mail::button url="''">
View Full Report
</x-mail::button>

Thanks,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
