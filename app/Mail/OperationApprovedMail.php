<?php

namespace App\Mail;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $status;

    public function __construct(Operation $operation, string $status)
    {
        $this->operation = $operation;
        $this->status = $status; // approved / rejected
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' ? 'Operation Approved' : 'Operation Rejected';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-approved',
            with: [
                'operation' => $this->operation,
                'status' => $this->status,
            ],
        );
    }
}