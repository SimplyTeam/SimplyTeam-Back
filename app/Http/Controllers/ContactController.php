<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\GithubRequest;
use App\Mail\SendContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMail(ContactRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ];
        Mail::to('SimplyTeam@outlook.com')->send(new SendContactMail($data));
    }

    public function createGitHubIssue(GithubRequest $request)
    {
        $validatedData = $request->validated();
        $organization = 'SimplyTeam'; // Remplacez par le nom de l'organisation
        $repository = 'gestion-des-incidents'; // Remplacez par le nom du dépôt

        $token = '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'; // Remplacez par votre jeton d'accès GitHub

        $url = "https://api.github.com/repos/{$organization}/{$repository}/issues";

        $data = array(
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'labels' => $validatedData['labels']
        );

        $headers = [
            "Authorization: Bearer {$token}",
            "Accept: application/vnd.github.v3+json",
            "Content-Type: application/json",
            "User-Agent: IssueTrackerApp"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            // L'issue a été créée avec succès
            return response()->json(['message' => 'Issue créée avec succès'], 201);
        } else {
            // Gérer les erreurs ici en fonction de la réponse de GitHub
            return response()->json(['error' => 'Échec de la création de l\'issue'], $httpCode);
        }
    }
}
