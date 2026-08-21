<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .header {
            background: #1a1a2e;
            padding: 32px 24px;
            text-align: center;
        }

        .header h1 {
            color: white;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 32px 24px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #1a1a2e;
        }

        .success {
            background: #e8f5e9;
            border-left: 3px solid #4caf50;
            padding: 12px 16px;
            margin: 20px 0;
            font-size: 14px;
            color: #2e7d32;
            border-radius: 6px;
        }

        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .cred-item {
            margin-bottom: 15px;
        }

        .cred-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .cred-value {
            background: white;
            padding: 10px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
            border: 1px solid #e0e0e0;
            word-break: break-all;
        }

        .button {
            display: block;
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 12px;
            text-decoration: none;
            border-radius: 6px;
            margin: 24px 0;
            font-weight: 500;
            font-size: 14px;
        }

        .button:hover {
            background: #16213e;
        }

        .instructions {
            background: #fef9e6;
            padding: 16px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
        }

        .instructions h3 {
            font-size: 14px;
            margin-bottom: 8px;
            color: #e65100;
        }

        .instructions ol {
            margin-left: 20px;
            color: #555;
        }

        .instructions li {
            margin: 6px 0;
        }

        .footer {
            background: #fafafa;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }

        .footer a {
            color: #1a1a2e;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bitchest</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $user->name }},
            </div>

            <div class="success">
                ✅ Votre inscription a été approuvée
            </div>

            <div class="credentials">
                <div class="cred-item">
                    <div class="cred-label">Email</div>
                    <div class="cred-value">{{ $user->email }}</div>
                </div>
                <div class="cred-item">
                    <div class="cred-label">Mot de passe temporaire</div>
                    <div class="cred-value">{{ $tempPassword }}</div>
                </div>
            </div>

            <a href="{{ $loginUrl }}" class="button">
                Se connecter →
            </a>

            <div class="instructions">
                <h3>📌 Important</h3>
                <ol>
                    <li>Changez votre mot de passe à la première connexion</li>
                    <li>Conservez ces identifiants en lieu sûr</li>
                </ol>
            </div>

            <p style="font-size: 13px; color: #888; margin-top: 20px;">
                Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.
            </p>
        </div>

        <div class="footer">
            <p>Bitchest · <a href="{{ $loginUrl }}">support@bitchest.com</a></p>
            <p style="margin-top: 8px;">© 2026 Tous droits réservés</p>
        </div>
    </div>
</body>
</html>