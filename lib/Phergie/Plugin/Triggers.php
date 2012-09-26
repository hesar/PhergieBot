<?php
    class Phergie_Plugin_Triggers extends Phergie_Plugin_Abstract {
        
        public function onLoad() {
            $this->getPluginHandler()->getPlugin('Command');
        }
        
        public function onCommandAddtrigger() {}
        
        public function onCommandDeltrigger() {}
        
        public function onPrivmsg() {
            if(strpos($this->event->getArgument(1),  $this->getConfig('command.prefix', '.') == 0)) {
                //sprawdzamy czy jest taki trigger, jezeli jest to go wolamy
            }
        }
        
    }
?>
