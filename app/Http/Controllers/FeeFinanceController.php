<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Course;
use App\Models\Department;
use App\Models\DepartmentBudget;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\FinancialVoucher;
use App\Models\FixedAsset;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Student;
use App\Models\StudentFeeAccount;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeeFinanceController extends Controller
{
    // Module 10: Fee & Student Accounts
    public function fees(): View
    {
        $structures = FeeStructure::with('course')->get();
        $accounts = StudentFeeAccount::with(['student.course', 'payments'])->latest()->get();
        $payments = FeePayment::with('student')->latest()->take(30)->get();
        $courses = Course::all();
        $students = Student::where('status', 'Active')->get();

        return view('fees.index', compact('structures', 'accounts', 'payments', 'courses', 'students'));
    }

    public function storeFeeStructure(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:12',
            'category' => 'required|in:All,General,OBC,SC,ST,EWS',
            'admission_fee' => 'numeric|min:0',
            'tuition_fee' => 'numeric|min:0',
            'exam_fee' => 'numeric|min:0',
            'library_fee' => 'numeric|min:0',
            'lab_fee' => 'numeric|min:0',
            'hostel_fee' => 'numeric|min:0',
            'transport_fee' => 'numeric|min:0',
            'misc_fee' => 'numeric|min:0',
        ]);
        $data['total_fee'] = ($data['admission_fee'] ?? 0) + ($data['tuition_fee'] ?? 0) + ($data['exam_fee'] ?? 0)
            + ($data['library_fee'] ?? 0) + ($data['lab_fee'] ?? 0) + ($data['hostel_fee'] ?? 0)
            + ($data['transport_fee'] ?? 0) + ($data['misc_fee'] ?? 0);

        FeeStructure::create($data);

        return back()->with('success', 'Fee structure created (Total: ₹' . number_format($data['total_fee'], 2) . ').');
    }

    public function collectFee(Request $request, StudentFeeAccount $account): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . max(1, $account->balance_amount),
            'payment_mode' => 'required|in:UPI,Card,NetBanking,Cash,Cheque,DD',
            'transaction_ref' => 'nullable|string|max:80',
            'remarks' => 'nullable|string',
        ]);

        $payment = FeePayment::create([
            'student_fee_account_id' => $account->id,
            'student_id' => $account->student_id,
            'receipt_no' => 'RCPT-' . date('Y') . '-' . rand(1000, 9999),
            'amount' => $data['amount'],
            'payment_mode' => $data['payment_mode'],
            'transaction_ref' => $data['transaction_ref'] ?? ('TXN' . rand(100000, 999999)),
            'payment_date' => now()->toDateString(),
            'status' => 'Completed',
            'remarks' => $data['remarks'] ?? null,
        ]);

        $newPaid = $account->paid_amount + $data['amount'];
        $newBalance = max(0, $account->total_amount - $account->concession_amount - $account->scholarship_amount + $account->late_penalty - $newPaid);

        $account->update([
            'paid_amount' => $newPaid,
            'balance_amount' => $newBalance,
            'status' => $newBalance <= 0 ? 'Paid' : 'Partial',
        ]);

        return back()->with('success', "Fee collected! Receipt #{$payment->receipt_no} generated.");
    }

    public function feeReceipt(FeePayment $payment): View
    {
        $payment->load(['student.course.department.college', 'feeAccount']);

        return view('fees.receipt', compact('payment'));
    }

    // Module 11: College Finance & Accounting
    public function finance(): View
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $vouchers = FinancialVoucher::with(['debitAccount', 'creditAccount', 'department'])->latest()->get();
        $budgets = DepartmentBudget::with('department')->get();
        $assets = FixedAsset::with('department')->get();
        $departments = Department::all();

        $totalIncome = $accounts->where('account_type', 'Income')->sum('balance');
        $totalExpense = $accounts->where('account_type', 'Expense')->sum('balance');
        $totalAssets = $accounts->where('account_type', 'Asset')->sum('balance');

        return view('finance.index', compact('accounts', 'vouchers', 'budgets', 'assets', 'departments', 'totalIncome', 'totalExpense', 'totalAssets'));
    }

    public function storeVoucher(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'voucher_type' => 'required|in:Payment,Receipt,Contra,Journal',
            'voucher_date' => 'required|date',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id|different:debit_account_id',
            'amount' => 'required|numeric|min:1',
            'gst_amount' => 'nullable|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'narration' => 'required|string',
        ]);
        $data['voucher_no'] = 'VCH-' . date('Y') . '-' . rand(100, 999);
        $data['gst_amount'] = $data['gst_amount'] ?? 0;
        $data['tds_amount'] = $data['tds_amount'] ?? 0;
        $data['created_by'] = Auth::id();

        FinancialVoucher::create($data);

        ChartOfAccount::where('id', $data['debit_account_id'])->increment('balance', $data['amount']);
        ChartOfAccount::where('id', $data['credit_account_id'])->increment('balance', $data['amount']);

        return back()->with('success', "Voucher {$data['voucher_no']} posted to General Ledger.");
    }

    // Module 16: Procurement & Inventory
    public function procurement(): View
    {
        $vendors = Vendor::all();
        $requisitions = PurchaseRequisition::with(['department', 'requester'])->latest()->get();
        $orders = PurchaseOrder::with(['vendor', 'requisition'])->latest()->get();
        $inventory = InventoryItem::with('department')->get();
        $departments = Department::all();

        return view('procurement.index', compact('vendors', 'requisitions', 'orders', 'inventory', 'departments'));
    }

    public function storeRequisition(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'item_description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:1',
        ]);
        $data['req_no'] = 'PR-' . date('Y') . '-' . rand(100, 999);
        $data['requested_by'] = Auth::id();
        PurchaseRequisition::create($data);

        return back()->with('success', 'Purchase requisition submitted.');
    }

    public function storePurchaseOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:1',
        ]);
        $data['po_no'] = 'PO-' . date('Y') . '-' . rand(100, 999);
        PurchaseOrder::create($data);

        if (! empty($data['purchase_requisition_id'])) {
            PurchaseRequisition::where('id', $data['purchase_requisition_id'])->update(['status' => 'PO Created']);
        }

        return back()->with('success', "Purchase Order {$data['po_no']} issued.");
    }
}

