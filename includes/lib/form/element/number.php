<?php
/**
 * MangaPress_Framework
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress
 */
require_once MP_ABSPATH . '/includes/lib/form/element/text.php';
/**
 * MangaPress_Number
 *
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @package MangaPress_Text
 * @version $Id$
 */
class MangaPress_Number extends MangaPress_Text
{

    /**
     * Echo form element
     *
     * @return string
     */
    public function __toString()
    {
        $label = '';
        if (!empty($this->_label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $desc = $this->get_description();
        $description = "";
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $attr = $this->build_attr_string();

        $htmlArray['content'] = "{$label}<input type=\"text\" $attr />\r\n{$description}";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }
}