<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\CitasMedicas;

class AppointmentRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;

    /**
     * Create a new message instance.
     */
    public function __construct(CitasMedicas $cita)
    {
        $this->cita = $cita;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Solicitud de Cita Médica - EPS SOG',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-request',
            with: [
                'cita' => $this->cita,
                'paciente' => $this->cita->paciente,
                'doctor' => $this->cita->doctor,
                'consultorio' => $this->cita->consultorio,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
