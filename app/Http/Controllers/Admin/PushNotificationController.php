<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PushNotificationController extends Controller
{
    public function __construct(private OneSignalService $oneSignal) {}

    /**
     * Show the compose form and notification history list.
     */
    public function index()
    {
        return view('admin.push-notifications.index');
    }

    /**
     * Server-side DataTable for notification history.
     */
    public function datatable(Request $request)
    {
        $notifications = PushNotification::select([
            'push_notification_id',
            'title',
            'recipient_mode',
            'total_recipient',
            'send_status',
            'created_by',
            'created_at',
        ]);

        return DataTables::of($notifications)
            ->addIndexColumn()
            ->editColumn('recipient_mode', fn ($n) =>
                '<span class="badge bg-' . ($n->recipient_mode === 'all' ? 'primary' : 'info text-dark') . '">'
                . ucfirst(e($n->recipient_mode)) . '</span>')
            ->editColumn('send_status', fn ($n) =>
                '<span class="badge bg-' . ($n->send_status === 'success' ? 'success' : 'danger') . '">'
                . ucfirst(e($n->send_status)) . '</span>')
            ->editColumn('created_at', fn ($n) => $n->created_at ? $n->created_at->format('d M Y, H:i') : '—')
            ->addColumn('action', fn ($n) =>
                '<button class="btn btn-sm btn-outline-primary btn-view-detail"'
                . ' data-id="' . $n->push_notification_id . '">'
                . '<i class="bi bi-eye me-1"></i>View</button>')
            ->rawColumns(['recipient_mode', 'send_status', 'action'])
            ->make(true);
    }

    /**
     * AJAX – return detail of a single push notification (for modal).
     */
    public function show(PushNotification $pushNotification)
    {
        return response()->json($pushNotification);
    }

    /**
     * AJAX – search users for Select2.
     */
    public function usersSearch(Request $request)
    {
        $term = trim($request->input('q', ''));

        $query = User::select('meem_code', 'fullname')
            ->whereNotNull('meem_code')
            ->where('status', '!=', 'deleted')
            ->where(function ($q) use ($term) {
                $q->where('meem_code', 'like', "%{$term}%")
                  ->orWhere('fullname', 'like', "%{$term}%");
            })
            ->orderBy('fullname')
            ->limit(50)
            ->get();

        $results = $query->map(fn ($u) => [
            'id'   => $u->meem_code,
            'text' => $u->meem_code . ' - ' . $u->fullname,
        ]);

        return response()->json(['results' => $results]);
    }

    /**
     * Handle the push-notification send form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'message'          => 'required|string',
            'recipient_mode'   => 'required|in:all,selected',
            'selected_users'   => 'required_if:recipient_mode,selected|array|min:1',
            'selected_users.*' => 'string',
            'image_url'        => 'nullable|url|max:2048',
            'additional_data_1'=> 'nullable|string|max:1000',
            'additional_data_2'=> 'nullable|string|max:1000',
            'additional_data_3'=> 'nullable|string|max:1000',
        ], [
            'selected_users.required_if' => 'Please select at least one recipient.',
            'selected_users.min'         => 'Please select at least one recipient.',
            'image_url.url'              => 'The image URL must be a valid URL.',
        ]);

        $recipientMode    = $validated['recipient_mode'];
        $selectedUsers    = $validated['selected_users'] ?? [];
        $title            = $validated['title'];
        $message          = $validated['message'];
        $imageUrl         = $validated['image_url'] ?? null;
        $additionalData1  = $validated['additional_data_1'] ?? null;
        $additionalData2  = $validated['additional_data_2'] ?? null;
        $additionalData3  = $validated['additional_data_3'] ?? null;

        // Determine created_by from the authenticated admin user
        $admin     = Auth::user();
        $createdBy = $admin ? ($admin->fullname ?? $admin->email ?? (string) $admin->getAuthIdentifier()) : null;

        // Call OneSignal
        if ($recipientMode === 'all') {
            $result = $this->oneSignal->sendToAll(
                $title, $message, $imageUrl, $additionalData1, $additionalData2, $additionalData3
            );
        } else {
            $result = $this->oneSignal->sendToSelected(
                $selectedUsers, $title, $message, $imageUrl, $additionalData1, $additionalData2, $additionalData3
            );
        }

        // Persist the log record
        PushNotification::create([
            'title'                     => $title,
            'message'                   => $message,
            'image_url'                 => $imageUrl,
            'recipient_mode'            => $recipientMode,
            'selected_meem_codes'       => $recipientMode === 'selected' ? json_encode($selectedUsers) : null,
            'additional_data_1'         => $additionalData1,
            'additional_data_2'         => $additionalData2,
            'additional_data_3'         => $additionalData3,
            'onesignal_app_id'          => $this->oneSignal->getAppId(),
            'onesignal_request_payload' => json_encode($result['payload'] ?? []),
            'onesignal_response'        => $result['raw_response'] ?? null,
            'onesignal_notification_id' => $result['notification_id'] ?? null,
            'total_recipient'           => $result['total_count'] ?? 0,
            'send_status'               => $result['success'] ? 'success' : 'failed',
            'error_message'             => $result['error'] ?? null,
            'created_by'                => $createdBy,
        ]);

        if ($result['success']) {
            return redirect()->route('admin.push-notifications.index')
                ->with('success', 'Push notification sent successfully to ' . $result['total_count'] . ' recipient(s).');
        }

        return redirect()->route('admin.push-notifications.index')
            ->with('error', 'Push notification failed: ' . ($result['error'] ?? 'Unknown error.'));
    }
}
