<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #007bff; margin: 0;">JobFlow</h1>
        </div>
        
        <h2 style="color: #444;">Réinitialisation de votre mot de passe</h2>
        
        <p>Bonjour,</p>
        
        <p>Vous avez demandé une réinitialisation de votre mot de passe pour votre compte JobFlow.</p>
        
        <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe (ce lien est valable 1 heure) :</p>
        
        <div style="text-align: center; margin: 40px 0;">
            <a href="<?= $resetLink ?>" style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Réinitialiser mon mot de passe</a>
        </div>
        
        <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité. Votre mot de passe restera inchangé.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
        
        <p style="font-size: 0.8em; color: #999; text-align: center;">
            &copy; <?= date('Y') ?> JobFlow
        </p>
    </div>
</body>
</html>
