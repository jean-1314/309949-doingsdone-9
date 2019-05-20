<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="/auth.php">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Вход на сайт</h2>

    <form class="form" action="" method="post" autocomplete="off">
        <?php
            $emailOrPasswordError = isset($wrongEmailOrPassword) && $wrongEmailOrPassword;
            $errorClassActive = ((isset($errors['email'])) || $emailOrPasswordError);
        ?>
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input <?= $errorClassActive ? 'form__input--error' : '' ?>" type="text" name="email" id="email" value="<?= $values['email'] ?? '' ?>" placeholder="Введите e-mail">
            <?php if (isset($errors['email'])) { ?>
                <p class="form__message"><?= $errors['email'] ?></p>
            <?php } else if ($emailOrPasswordError) { ?>
                <p class="form__message"><?= $wrongEmailOrPasswordText ?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input <?= $emailOrPasswordError ? 'form__input--error' : '' ?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
            <?php if (isset($errors['password'])) { ?>
                <p class="form__message"><?= $errors['password'] ?></p>
            <?php } else if ($emailOrPasswordError) { ?>
                <p class="form__message"><?= $wrongEmailOrPasswordText ?></p>
            <?php } ?>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>

</main>
