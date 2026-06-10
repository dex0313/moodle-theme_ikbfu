<?php
namespace theme_ikbfu\output;

defined('MOODLE_INTERNAL') || die();

class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Рендерим navbar с добавленными категориями курса.
     */
    public function navbar(): string {
        global $PAGE, $COURSE;

        // Только на главной странице курса
        if ($COURSE->id != SITEID &&
            $PAGE->context->contextlevel == CONTEXT_COURSE) {
            $this->inject_category_breadcrumbs();
        }

        return parent::navbar();
    }

    /**
     * Добавляет категории в $PAGE->navbar.
     */
    protected function inject_category_breadcrumbs(): void {
        global $COURSE, $PAGE;

        if (empty($COURSE->category)) {
            return;
        }

        // Собираем цепочку категорий от корня
        $categories = [];
        try {
            $cat = \core_course_category::get($COURSE->category, IGNORE_MISSING);
            while ($cat) {
                array_unshift($categories, $cat);
                $cat = $cat->parent
                    ? \core_course_category::get($cat->parent, IGNORE_MISSING)
                    : null;
            }
        } catch (\Exception $e) {
            return;
        }

        // Добавляем категории в начало хлебных крошек
        // (они окажутся перед тем, что уже есть в navbar)
        foreach (array_reverse($categories) as $category) {
            // Проверяем, не добавлена ли уже эта категория
            if (!$PAGE->navbar->get($category->id, \navigation_node::TYPE_CATEGORY)) {
                $PAGE->navbar->add(
                    $category->get_formatted_name(),
                    new \moodle_url('/course/index.php', ['categoryid' => $category->id]),
                    \navigation_node::TYPE_CATEGORY
                );
            }
        }
    }
}