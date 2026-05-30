<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur JobFlow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #007bff; margin: 0;">JobFlow</h1>
        </div>
        
        <h2 style="color: #444;">Bienvenue <?= e($prenom) ?> !</h2>
        
        <p>Merci de vous être inscrit sur JobFlow, l'outil simplifié pour gérer vos devis et factures.</p>
        
        <p>Pour activer votre compte et commencer dès maintenant, cliquez sur le bouton ci-dessous :</p>
        
        <div style="text-align: center; margin: 40px 0;">
            <a href="<?= $fullUrl ?>" style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Activer mon compte</a>
        </div>
        
        <p style="font-size: 0.9em; color: #777;">
            Si le bouton ne fonctionne pas, vous pouvez copier-coller ce lien dans votre navigateur :<br>
            <a href="<?= $fullUrl ?>" style="color: #007bff;"><?= $fullUrl ?></a>
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
        
        <p style="font-size: 0.8em; color: #999; text-align: center;">
            Ceci est un email automatique, merci de ne pas y répondre.<br>
            &copy; <?= date('Y') ?> JobFlow
        </p>
    </div>
</body>
</html>


