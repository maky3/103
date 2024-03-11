<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $date = $request->query('date');

        $tickets = Ticket::query();

        if ($status) {
            $tickets->where('status', $status);
        }

        if ($date) {
            $tickets->whereDate('created_at', $date);
        }

        return response()->json($tickets->get(), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = Ticket::create($validator->validated());

        return response()->json($ticket, 201);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('update', $ticket);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket->comment = $validator->validated()['comment'];
        $ticket->status = 'Resolved';
        $ticket->save();

        Mail::to($ticket->email)->send(new TicketResponse($ticket));

        return response()->json($ticket, 200);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(null, 204);
    }

    public function attachFile(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('update', $ticket);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('public/ticket_attachments', $filename);

            // Привязка файла к заявке
            $ticket->attachments()->create([
                'path' => $path,
                'filename' => $filename,
            ]);
        }

        return response()->json($ticket->load('attachments'), 200);
    }

    public function downloadAttachment($ticketId, $attachmentId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $attachment = $ticket->attachments()->findOrFail($attachmentId);

        if (!Storage::exists($attachment->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($attachment->path, $attachment->filename);
    }
}
