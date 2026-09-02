<?php

namespace App\Mail;

use App\Models\Operation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $operation;
    public $creator;

    public function __construct(Operation $operation, User $creator)
    {
        $this->operation = $operation;
        $this->creator = $creator;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Operation Pending Approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.operation-created',
            with: [
                'operation' => $this->operation,
                'creator' => $this->creator,
            ],
        );
    }
}