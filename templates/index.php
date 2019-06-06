<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $key => $project) { ?>
            <li class="main-navigation__list-item <?= isset($_GET['id']) && $_GET['id'] == $project['id'] ? 'main-navigation__list-item--active' : '' ?>">
                <a class="main-navigation__list-item-link" href="/?id=<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></a>
                <span class="main-navigation__list-item-count"><?= $project['tasks_count'] ?></span>
            </li>
            <?php } ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
        href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <?php
                function setFilter($filter):string
                {
                    return isset($_GET['id']) ? '?id=' . $_GET['id'] . '&filter=' . $filter : '?filter=' . $filter;
                }
                function isActive($filter):string
                {
                    return isset($_GET['filter']) && $_GET['filter'] === $filter;
                }
            ?>
            <a href="<?= isset($_GET['id']) ? '?id=' . $_GET['id'] : '/' ?>" class="tasks-switch__item <?= isset($_GET['filter']) ?: 'tasks-switch__item--active' ?>">Все задачи</a>
            <a href="<?= setFilter('today') ?>" class="tasks-switch__item <?= isActive('today') ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>
            <a href="<?= setFilter('tomorrow') ?>" class="tasks-switch__item <?= isActive('tomorrow') ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
            <a href="<?= setFilter('expired') ?>" class="tasks-switch__item <?= isActive('expired') ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $show_completed_tasks === 1 ? 'checked' : '' ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <?php foreach ($tasks as $key => $task) { ?>
            <tr
                class="tasks__item task
                <?= $task['status'] === 1 ? 'task--completed' : '' ?>
                <?= $task['status'] === 1 && $show_completed_tasks === 0 ? 'visually-hidden' : '' ?>
                <?= $task['deadline'] && isDeadlineClose($task['deadline']) ? 'task--important' : '' ?>"
            >
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?= $task['id'] ?>" <?= $task['status'] === 1 ? 'checked' : '' ?>>
                        <span class="checkbox__text"><?= htmlspecialchars($task['title']) ?></span>
                    </label>
                </td>

                <td class="task__file">
                    <?php if ($task['file_name']) { ?>
                        <a class="download-link" href="<?= '/' . $task['file_name'] ?>"><?= $task['file_name'] ?></a>
                    <?php } ?>
                </td>

                <td class="task__date"><?= $task['deadline'] ? htmlspecialchars(date('d.m.Y', strtotime($task['deadline']))) : '' ?></td>
                <td class="task__controls"></td>

            </tr>
        <?php } ?>
    </table>
    <?php if (empty($tasks)) { ?>
        <p>Ничего не найдено по вашему запросу</p>
    <?php } ?>
</main>


