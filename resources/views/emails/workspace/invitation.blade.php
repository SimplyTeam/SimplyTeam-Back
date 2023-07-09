<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation à rejoindre {{ $workspace_name }}</title>
</head>
<body>
<h1>Bienvenue sur {{ $workspace_name }}</h1>

<p>Bonjour {{ $user_name }},</p>

<p>{{ $current_user_name }} vous a invité(e) à rejoindre l'espace de travail {{ $workspace_name }} sur SimplyTeam. Pour
    accepter l'invitation, veuillez cliquer sur le lien suivant :</p>

<p><a href="{{ $invitation_url }}">Rejoindre {{ $workspace_name }}</a></p>

<p>Merci !</p>

</body>
</html>
