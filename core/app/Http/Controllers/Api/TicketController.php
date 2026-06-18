<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    protected array $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'];

    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return getResponse('ticket_list', 'success', 'Support tickets', [
            'tickets'   => $tickets,
            'next_page' => $tickets->nextPageUrl(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'  => 'required|max:255',
            'priority' => 'required|in:1,2,3',
            'message'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $ticket             = new SupportTicket();
        $ticket->user_id    = $user->id;
        $ticket->ticket     = rand(100000, 999999);
        $ticket->name       = $user->fullname;
        $ticket->email      = $user->email;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = 0;
        $ticket->priority   = $request->priority;
        $ticket->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title   = 'New support ticket has opened';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        return getResponse('ticket_created', 'success', 'Ticket opened successfully', [
            'ticket' => $ticket,
        ]);
    }

    public function show($ticketNo)
    {
        $ticket = SupportTicket::where('ticket', $ticketNo)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $messages = SupportMessage::where('support_ticket_id', $ticket->id)
            ->with('admin', 'attachments')
            ->orderBy('id', 'asc')
            ->get();

        return getResponse('ticket_view', 'success', 'Ticket details', [
            'ticket'   => $ticket,
            'messages' => $messages,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $message                    = new SupportMessage();
        $ticket->status             = 2;
        $ticket->last_reply         = Carbon::now();
        $ticket->save();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        return getResponse('ticket_reply', 'success', 'Reply sent successfully', [
            'message' => $message,
        ]);
    }

    public function close($id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $ticket->status = 3;
        $ticket->save();

        return getResponse('ticket_closed', 'success', 'Ticket closed successfully', [
            'ticket' => $ticket,
        ]);
    }

    public function downloadAttachment($id)
    {
        $attachment = SupportAttachment::findOrFail($id);
        $message    = $attachment->supportMessage;

        abort_unless(
            $message && $message->ticket && $message->ticket->user_id === auth()->id(),
            403
        );

        $path     = getFilePath('ticket') . '/' . $attachment->attachment;
        abort_unless(is_file($path), 404);

        return response()->file($path);
    }
}
