<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation to join {{ $workspace_name }}</title>
</head>
<body>
<h1>Welcome to {{ $workspace_name }}</h1>

<p>Hello {{ $user_name }},</p>

<p>{{ $current_user_name }} has invited you to join the {{ $workspace_name }} workspace on SimplyTeam. To accept the invitation, please click the following link:</p>

<p><a href="{{ $invitation_url }}">Join {{ $workspace_name }}</a></p>

<p>Thank you!</p>
</body>
</html>
