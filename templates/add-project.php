<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $key => $project) { ?>
                <li class="main-navigation__list-item">
                    <a class="main-navigation__list-item-link" href="/?id=<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></a>
                    <span class="main-navigation__list-item-count"><?= $project['tasks_count'] ?></span>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?= $error ? 'form__input--error' : '' ?>" type="text" name="title" id="project_name" value="" placeholder="Введите название проекта">
            <?php if ($error) { ?>
                <p class="form__message"><?= $errorText ?></p>
            <?php } ?>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
