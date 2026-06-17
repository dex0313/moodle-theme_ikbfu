<?php
namespace theme_ikbfu\output;

defined('MOODLE_INTERNAL') || die();

class core_renderer extends \theme_boost\output\core_renderer {

    public function navbar(): string {
        // Используем наш boostnavbar вместо boost'овского
        $newnav = new \theme_ikbfu\boostnavbar($this->page);
        return $this->render_from_template('core/navbar', $newnav);
    }
}