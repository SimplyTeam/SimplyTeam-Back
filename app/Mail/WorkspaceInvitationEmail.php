<?php

namespace App\Mail;

use App\Models\WorkspaceInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkspaceInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $workspaceInvitation;
    public $invitationUrl;

    /**
     * Create a new message instance.
     *
     * @param WorkspaceInvitation $workspaceInvitation
     * @param string $invitationUrl
     */
    public function __construct(WorkspaceInvitation $workspaceInvitation, string $invitationUrl)
    {
        $this->workspaceInvitation = $workspaceInvitation;
        $this->invitationUrl = $invitationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $workspaceName = $this->workspaceInvitation->workspace->name;

        return $this->markdown('emails.workspace.invitation')
            ->with([
                'workspace_name' => $workspaceName,
                'invitation_url' => $this->invitationUrl,
                'current_user_name' => $this->workspaceInvitation->invitedBy->name,
                'user_name' => $this->workspaceInvitation->name,
            ])
            ->subject('Invitation à rejoindre le workspace ' . $workspaceName);
    }
}
