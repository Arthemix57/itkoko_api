<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification de mot de passe - ITKOKO</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #f5f5f5; /* Couleur de fond neutre */
            color: #333333;
            padding: 20px 0; /* Ajuste le padding */
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        .header img {
            width: 150px; /* Taille du logo */
            height: auto; /* Maintient les proportions */
            margin-bottom: 10px; /* Espacement sous le logo */
        }
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 20px 0;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px;
            text-align: center;
            background-color: #ffffff;
        }
        .content h2 {
            color: #d32f2f; /* Couleur du texte */
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        .content p {
            color: #666666;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .footer {
            padding: 30px;
            text-align: center;
            background-color: #f5f5f5; /* Couleur de fond neutre */
            color: #888888;
            font-size: 14px;
            border-top: 1px solid #e0e0e0;
        }
        .footer a {
            color: #d32f2f;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path() . '/logos/logo.png') }}" alt="Logo ITKOKO">
            <h1>Modification de votre mot de passe</h1>
        </div>
        <div class="content">
            <h2>Bonjour, {{ $user->name }} !</h2>
            <p>
                Nous vous informons que votre mot de passe a été modifié avec succès sur <strong>ITKOKO</strong>.
            </p>
            <p>
                Date de la modification : {{ $date }} <!-- Affiche la date et l'heure de la modification -->
            </p>
            <p>
                Si vous n'êtes pas à l'origine de cette modification ou si vous avez des questions, veuillez contacter notre service client immédiatement.
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2025 ITKOKO. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
