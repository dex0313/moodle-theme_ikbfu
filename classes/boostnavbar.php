<?php
namespace theme_ikbfu;

defined('MOODLE_INTERNAL') || die();

class boostnavbar extends \theme_boost\boostnavbar {

    protected function prepare_nodes_for_boost(): void {

        // Вызываем оригинальный метод — он вырезает категории и курс
        parent::prepare_nodes_for_boost();

        // Восстанавливаем только на главной странице курса
        if ($this->page->context->contextlevel == CONTEXT_COURSE
                && $this->page->course->id != SITEID
                && !str_starts_with($this->page->pagetype, 'course-view-section-')) {

            $course = $this->page->course;
            $categoryitems = [];

            // Строим цепочку категорий от корня через API
            if (!empty($course->category)) {
                $category = \core_course_category::get($course->category, IGNORE_MISSING);
                while ($category) {
                    // Создаём навигационный узел для категории
                    $node = \navigation_node::create(
                        $category->get_formatted_name(),
                        new \moodle_url('/course/index.php', ['categoryid' => $category->id]),
                        \navigation_node::TYPE_CATEGORY,
                        null,
                        $category->id
                    );
                    array_unshift($categoryitems, new \breadcrumb_navigation_node($node));

                    // Идём вверх по дереву категорий
                    $category = $category->parent
                        ? \core_course_category::get($category->parent, IGNORE_MISSING)
                        : null;
                }
            }

            // Вставляем категории в начало
            $this->items = array_merge(
                $categoryitems,
                $this->items
            );

            // Последний элемент не должен быть ссылкой
            // $this->remove_last_item_action();
                    
            
        }
    }
}
