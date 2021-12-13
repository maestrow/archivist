<form class="login" method="post">
  <a class="login__logo-link" href="." title="На главную">
    <img class="login__logo" src="images/logo.svg" alt="лого" />
    <div>
      <div class="login__title"><?php echo $title ?></div>
      <div class="login__subtitle"><?php echo $subtitle ?></div>
    </div>
  </a>
  <input type="hidden" name="action" value="login">

    <?php if (isset($errorMessage)) { ?>
      <div class="r-errorMessage"><?php echo $errorMessage ?></div>
    <?php } ?>

    <div>
      <div class="r-field">
        <label for="login">Login</label>
        <input type="text" name="login" placeholder="Your login" required autofocus maxlength="100" />
      </div>

      <div class="r-field">
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Your password" required maxlength="20" />
      </div>
    </div>

    <div class="login__buttons">
      <input type="submit" value="Sign in" />
    </div>
</form>