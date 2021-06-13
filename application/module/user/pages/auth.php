<?php
/** @var array $pageData */
?>

<div class="main-content">

    <div class="form-wrapper">
        <h2 class="form-title">ВХОД</h2>

        <div class="form-body">

            <form action="" method="post">
                <div class="fieldset">
                    <input id="login" type="text" name="login"
                           value="<?= htmlspecialchars($_POST['login'] ?? '', ENT_QUOTES) ?: '' ?>">
                    <label class="movable" for="login">Логин</label>
                </div>

                <div class="fieldset">
                    <input id="password" type="password" name="password">
                    <label class="movable" for="password">Пароль</label>

                </div>
                <div class="fieldset">
                    <button type="submit" name="submit_auth">ВОЙТИ</button>
                </div>
            </form>

            <?php if (!empty($pageData['errors'])) : ?>
                <div class="errors">
                    <?php foreach ($pageData['errors'] as $item) : ?>
                        <p><?= htmlspecialchars($item, ENT_QUOTES) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>
