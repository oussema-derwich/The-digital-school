<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bigscreen.com',
            'password' => Hash::make('password123'),
        ]);

        // Créer les 20 questions du sondage selon le cahier des charges Bigscreen
        $questions = [
            [
                'question_number' => 1,
                'title' => 'Votre adresse e-mail',
                'content' => 'Veuillez fournir votre adresse e-mail.',
                'type' => 'B',
                'options' => null
            ],
            [
                'question_number' => 2,
                'title' => 'Votre âge',
                'content' => 'Quel est votre âge ?',
                'type' => 'B',
                'options' => null
            ],
            [
                'question_number' => 3,
                'title' => 'Votre sexe',
                'content' => 'Quel est votre sexe ?',
                'type' => 'A',
                'options' => ['Homme', 'Femme', 'Préférer pas dire']
            ],
            [
                'question_number' => 4,
                'title' => 'Nombre de personnes dans votre foyer',
                'content' => 'Combien de personnes sont dans votre foyer (adultes et enfants) ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 5,
                'title' => 'Votre profession',
                'content' => 'Quelle est votre profession ?',
                'type' => 'B',
                'options' => null
            ],
            [
                'question_number' => 6,
                'title' => 'Quel casque VR utilisez-vous ?',
                'content' => 'Quel casque VR utilisez-vous ?',
                'type' => 'A',
                'options' => ['Oculus Quest', 'Oculus Rift/s', 'HTC Vive', 'Windows Mixed Reality', 'Valve index']
            ],
            [
                'question_number' => 7,
                'title' => 'Où achetez-vous du contenu VR ?',
                'content' => 'Quel store d\'applications utilisez-vous pour acheter du contenu VR ?',
                'type' => 'A',
                'options' => ['SteamVR', 'Occulus store', 'Viveport', 'Windows store']
            ],
            [
                'question_number' => 8,
                'title' => 'Quel casque prévoyez-vous d\'acheter ?',
                'content' => 'Quel casque prévoyez-vous d\'acheter dans un avenir proche ?',
                'type' => 'A',
                'options' => ['Occulus Quest', 'Occulus Go', 'HTC Vive Pro', 'PSVR', 'Autre', 'Aucun']
            ],
            [
                'question_number' => 9,
                'title' => 'Utilisateurs de Bigscreen dans votre foyer',
                'content' => 'Combien de personnes dans votre foyer utilisent votre casque VR pour regarder Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 10,
                'title' => 'Utilisation principale de Bigscreen',
                'content' => 'Pour quoi utilisez-vous principalement Bigscreen ?',
                'type' => 'A',
                'options' => ['Regarder la TV en direct', 'Regarder des films', 'Travailler', 'Jouer en solo', 'Jouer en équipe']
            ],
            [
                'question_number' => 11,
                'title' => 'Évaluation de la qualité d\'image',
                'content' => 'Comment évalueriez-vous la qualité d\'image sur Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 12,
                'title' => 'Évaluation du confort de l\'interface',
                'content' => 'Comment évalueriez-vous le confort d\'utilisation de l\'interface Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 13,
                'title' => 'Évaluation de la connexion réseau',
                'content' => 'Comment évalueriez-vous la connexion réseau de Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 14,
                'title' => 'Évaluation de la qualité des graphismes 3D',
                'content' => 'Comment évalueriez-vous la qualité des graphismes 3D dans Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 15,
                'title' => 'Évaluation de la qualité audio',
                'content' => 'Comment évalueriez-vous la qualité audio dans Bigscreen ?',
                'type' => 'C',
                'options' => null
            ],
            [
                'question_number' => 16,
                'title' => 'Notifications plus précises',
                'content' => 'Souhaitez-vous avoir des notifications plus précises pendant vos sessions Bigscreen ?',
                'type' => 'A',
                'options' => ['Oui', 'Non']
            ],
            [
                'question_number' => 17,
                'title' => 'Inviter un ami via smartphone',
                'content' => 'Souhaitez-vous inviter un ami à rejoindre votre session via son smartphone ?',
                'type' => 'A',
                'options' => ['Oui', 'Non']
            ],
            [
                'question_number' => 18,
                'title' => 'Enregistrer des émissions de TV',
                'content' => 'Souhaitez-vous enregistrer des émissions de TV pour les regarder plus tard ?',
                'type' => 'A',
                'options' => ['Oui', 'Non']
            ],
            [
                'question_number' => 19,
                'title' => 'Jeux exclusifs sur Bigscreen',
                'content' => 'Souhaitez-vous jouer à des jeux exclusifs sur votre Bigscreen ?',
                'type' => 'A',
                'options' => ['Oui', 'Non']
            ],
            [
                'question_number' => 20,
                'title' => 'Suggestion de nouvelle fonctionnalité',
                'content' => 'Quelle nouvelle fonctionnalité devrait exister sur Bigscreen ?',
                'type' => 'B',
                'options' => null
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}