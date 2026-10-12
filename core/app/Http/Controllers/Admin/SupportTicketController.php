<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->user = auth()->guard('admin')->user();
            return $next($request);
        });

        $this->userType = 'admin';
        $this->column   = 'admin_id';
    }

    public function tickets()
    {
        $pageTitle = 'Support Tickets';
        $items     = SupportTicket::clientTickets()->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function pendingTicket()
    {
        $pageTitle = 'Pending Tickets';
        $items     = SupportTicket::clientTickets()->whereIn('status', [0, 2]);

        if (request()->search) {
            $items = $items->whereHas('user', function ($user) {
                $user->where('username', request()->search);
            });
        }

        $items = $items->orderBy('id', 'desc')->with('user')->paginate(getPaginate());

        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function closedTicket()
    {
        $pageTitle = 'Closed Tickets';
        $items     = SupportTicket::clientTickets()->where('status', 3)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function answeredTicket()
    {
        $pageTitle = 'Answered Tickets';
        $items     = SupportTicket::clientTickets()->orderBy('id', 'desc')->with('user')->where('status', 1)->paginate(getPaginate());
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function contactSubmissions()
    {
        $pageTitle = 'Investment Inquiries';
        $items     = $this->contactQuery()->paginate(getPaginate());
        return view('admin.support.contacts', compact('items', 'pageTitle'));
    }

    public function pendingContactSubmissions()
    {
        $pageTitle = 'New Investment Inquiries';
        $items     = $this->contactQuery()->whereIn('status', [0, 2])->paginate(getPaginate());
        return view('admin.support.contacts', compact('items', 'pageTitle'));
    }

    public function contactShow($id)
    {
        $ticket    = SupportTicket::contactSubmissions()->with('supportMessage')->findOrFail($id);
        $pageTitle = 'Investment Inquiry';
        $lead      = $ticket->leadFields();
        return view('admin.support.contact_lead', compact('ticket', 'pageTitle', 'lead'));
    }

    public function contactStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,3',
        ]);

        $ticket = SupportTicket::contactSubmissions()->findOrFail($id);
        $ticket->status = (int) $request->status;
        $ticket->save();

        $notify[] = ['success', 'Inquiry status updated'];
        return back()->withNotify($notify);
    }

    protected function contactQuery()
    {
        $query = SupportTicket::contactSubmissions()->with('supportMessage')->orderByDesc('id');

        if ($reason = request('reason')) {
            $query->where('subject', $reason);
        }

        $search = trim((string) request('search'));
        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function ticketReply($id)
    {
        $ticket = SupportTicket::with('user')->where('id', $id)->firstOrFail();
        if ($ticket->isContactSubmission()) {
            return redirect()->route('admin.contact.show', $ticket->id);
        }
        $pageTitle = 'Reply Ticket';
        $messages  = SupportMessage::with('ticket', 'admin', 'attachments')->where('support_ticket_id', $ticket->id)->orderBy('id', 'desc')->get();
        return view('admin.support.reply', compact('ticket', 'messages', 'pageTitle'));
    }

    public function ticketDelete($id)
    {
        $message = SupportMessage::findOrFail($id);
        $path    = getFilePath('ticket');
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path . '/' . $attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', "Support ticket deleted successfully"];
        return back()->withNotify($notify);

    }

}
