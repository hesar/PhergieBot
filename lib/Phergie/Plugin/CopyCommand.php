<?php

class Phergie_Plugin_CopyCommand extends Phergie_Plugin_Abstract
{
    public function onAction() {
        
        $this->doPrivmsg($this->event->getSource(), var_export($this));
    }
    private function multi_implode($glue, $array) {
        $ret = '';

        foreach ($array as $item) {
            if (is_array($item)) {
                $ret .= $this->multi_implode($glue, $array);
            } else {
                $ret .= $item . $glue;
            }
        }

        $ret = substr($ret, 0, 100);

        return $ret;
    }
}
?>
