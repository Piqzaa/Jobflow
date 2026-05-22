<section class="auth-form">
  <h1>Nouveau mot de passe</h1>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>
  <?php if (isset($success)): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <form action="<?= url('/reset-password') ?>" method="POST">
    <!-- Protection CSRF -->
    <?php csrf_field(); ?>

    <div class="form-group">
      <label for="password">Nouveau mot de passe</label>
      <input type="password" name="password" id="password" required>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
      <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>

    <input type="hidden" name="token" value="<?= $token ?>">

    <button type="submit" class="btn">Modifier le mot de passe</button>
  </form>

  <p>Retour à la <a href="<?= url('/login') ?>">connexion</a></p>
</section>