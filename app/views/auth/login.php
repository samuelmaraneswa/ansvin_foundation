<?php
use App\Core\Config;
use App\Core\FlashMessage;

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | Ansvin Foundation</title>
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/style.css">
</head>
<body>
  <div class="login-container">
    <?php FlashMessage::show();?>

    <h2><?=$title;?></h2>

    <form class="form-login" method="POST" action="<?= Config::get('base_url') ?>/auth/login">
      <div>
        <label>Username:</label>
        <input type="text" name="username" required>
      </div>

      <div>
        <label>Password:</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
