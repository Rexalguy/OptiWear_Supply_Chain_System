<x-mail::message>
<style>
    /* Inline styles for email compatibility */
    h1, h2 {
        color: #2d3748;
        font-family: Arial, Helvetica, sans-serif;
        margin-bottom: 10px;
    }
    p, td, th, ul, li {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        color: #444;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        background: #f9f9f9;
    }
    th, td {
        border: 1px solid #e2e8f0;
        padding: 10px 12px;
        text-align: left;
    }
    th {
        background: #edf2f7;
        color: #2d3748;
        font-weight: bold;
    }
    .status-delivered { color: #38a169; font-weight: bold; }
    .status-confirmed { color: #3182ce; font-weight: bold; }
    .status-pending { color: #dd6b20; font-weight: bold; }
    .status-cancelled { color: #e53e3e; font-weight: bold; }
    .panel-notes {
        background: #f1f5f9;
        border-left: 4px solid #3182ce;
        padding: 16px;
        margin-bottom: 24px;
    }
</style>

<h1>Weekly Raw Materials Purchase Report</h1>

<p>Hi <strong>{{ $user->name }}</strong>,</p>
<p>
    Here is your weekly raw materials purchase report for the week of <strong>{{ $date }}</strong>.<br>
    This summary includes all raw materials purchased from suppliers, with quantities, costs, and notes.<br>
    Purchase orders and their statuses are also included.<br>
    Please review the attached report for full details.
</p>

<h2>Purchase Order Status Summary</h2>
<table>
    <thead>
        <tr>
            <th>Purchase Order Status</th>
            <th>Number of Orders</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="status-delivered">Delivered</td>
            <td class="status-delivered">{{ $deliveredCount }}</td>
        </tr>
        <tr>
            <td class="status-confirmed">Confirmed</td>
            <td class="status-confirmed">{{ $confirmedCount }}</td>
        </tr>
        <tr>
            <td class="status-pending">Pending</td>
            <td class="status-pending">{{ $pendingCount }}</td>
        </tr>
        <tr>
            <td class="status-cancelled">Cancelled</td>
            <td class="status-cancelled">{{ $cancelledCount }}</td>
        </tr>
        <tr>
            <td>Total Orders</td>
            <td>{{ $totalCount }}</td>
    </tbody>
</table>

<h2>Raw Materials Stock Summary</h2>
<table>
    <thead>
        <tr>
            <th>Stock Status</th>
            <th>Number</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Still in Stock</td>
            <td>{{ $still }}</td>
        </tr>
        <tr>
            <td>Running Low</td>
            <td>{{ $low }}</td>
        </tr>
        <tr>
            <td>Out of Stock</td>
            <td>{{ $out }}</td>
        </tr>
    </tbody>
</table>

<div class="panel-notes">
    <h2 style="margin-top:0;">Important Notes</h2>
    <ul>
        <li>Check all raw materials for quality upon delivery.</li>
        <li>Follow up on pending orders to avoid production delays.</li>
        <li>Maintain accurate records of all transactions.</li>
    </ul>
</div>

<x-mail::button :url="''">
    View Full Report
</x-mail::button>

<p style="margin-top: 32px;">
    Regards,<br>
    <strong>{{ config('app.name') }}</strong>
</p>
</x-mail::message>
