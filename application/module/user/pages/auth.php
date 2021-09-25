<?php
/** @var array $PAGE_DATA */
?>

<main id="application-auth">
    <div class="auth-header auth-header-2">
        <div class="logo">
            Отдел кадров
        </div>
        <div class="menu">
            <a href="/" class="menu-item">Авторизация</a>
        </div>
    </div>
    <div class="authorization">
        <div class="auth-form">
            <h2 class="auth-form-title">ВХОД</h2>

            <div class="auth-form-body">

                <form action="" method="post">
                    <div class="fieldset">
                        <input id="login" type="text" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '', ENT_QUOTES) ?: '' ?>" required>
                        <label class="movable" for="login">Логин</label>
                    </div>

                    <div class="fieldset">
                        <input id="password" type="password" name="password" required>
                        <label class="movable" for="password">Пароль</label>

                    </div>
                    <div class="fieldset">
                        <button type="submit" name="submit_auth">ВОЙТИ</button>
                    </div>
                </form>

                <?php if (!empty($PAGE_DATA['errors'])) : ?>
                    <div class="errors">
                        <?php foreach ($PAGE_DATA['errors'] as $item) : ?>
                            <p><?= htmlspecialchars($item, ENT_QUOTES) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

