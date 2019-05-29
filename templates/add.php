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
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="form__row">
            <?php $classname = isset($errors['title']) ? "form__input--error" : "";
            $value = isset($task['title']) ? $task['title'] : ""; ?>

            <label class="form__label" for="title">Название <sup>*</sup></label>

            <input class="form__input <?= $classname ?>" type="text" name="title" id="title" value="<?= $value ?>" placeholder="Введите название">
            <?php if (isset($errors['title'])) { ?>
                <p class="form__message"><?= $errors['title'] ?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <?php $classname = isset($errors['project']) ? "form__input--error" : "";
            $value = isset($task['project']) ? $task['project'] : ""; ?>

            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?= $classname ?>" name="project" id="project">
                <?php foreach ($projects as $key => $project) { ?>
                    <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                <?php } ?>
            </select>

            <?php if (isset($errors['project'])) { ?>
                <p class="form__message"><?= $errors['project'] ?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <?php $classname = isset($errors['date']) ? "form__input--error" : "";
            $value = isset($task['date']) ? $task['date'] : ""; ?>

            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input <?= $classname ?> form__input--date" type="text" name="date" id="date" value="" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors['date'])) { ?>
                <p class="form__message"><?= $errors['date'] ?></p>
            <?php } ?>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="file" id="file" value="">

            <label class="button button--transparent" for="file">
                <span>Выберите файл</span>
            </label>
        </div>

        <div class="form__row form__row--controls">
            <?php if ($errors) { ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php } ?>
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
