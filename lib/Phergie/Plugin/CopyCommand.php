<?php

class Phergie_Plugin_CopyCommand extends Phergie_Plugin_Abstract
{
      public static $messageStack = '';
      public static $users;
      protected $pluginConfig;
      protected $channelFrom;
      protected $channelTo;
      protected $hostFrom;
      protected $hostTo;
      protected $usersPlugin;
      private $startListening = false;
      private $slap = false;
      private $slapNick; //nick of slapper ;)
      private $counter = 0;

      /**
       * set up some configuration and check if it's all necessary 
       */
      public function onLoad() {
          $this->pluginConfig = $this->getConfig('copycommand.channels');
          if(!$this->pluginConfig) die('Configuration not set properly. Check copycommand.channels array or disbale CopyCommand plugin!');
          $this->channelFrom = $this->pluginConfig['channelFrom'][1];
          $this->channelTo = $this->pluginConfig['channelTo'][1];
          $this->hostFrom = $this->pluginConfig['channelFrom'][0];
          $this->hostTo = $this->pluginConfig['channelTo'][0];
          $this->usersPlugin = $this->getPluginHandler()->getPlugin('UserInfo');
      }
      
      /**
       * gets action from $channelFrom (slap), checks if it is @ or + and sets flag for slap whole
       * $channelTo in next tick
       * also sets flag for startListening for true 
       */
      public function onAction() {
          if($this->getConnection()->getHost() == $this->hostFrom && $this->event->getSource() == $this->channelFrom && $this->hasSufficientPrivileges($this->event->getNick(),$this->event->getSource()))
          {
              $this->startListening = true;
              $this->slap = true;
              $this->slapNick = $this->event->getNick();
              $this->counter = $this->getConfig('copycommand.counter');
          }
    }
    
    public function onResponse() {    }

    /**
     * 1. check if request comes from correct channel 
     * 2. check if it is message from the user that slapped (we KNOW that it is a user with sufficient privileges
     * 3. push message and decrement counter
     */
    public function onPrivmsg() {
        if($this->startListening && $this->getConnection()->getHost() == $this->hostFrom && $this->counter != 0) {
            if($this->getEvent()->getNick() == $this->slapNick) {
                $dottedNick = substr($this->slapNick, 0, 1).'.'.substr($this->slapNick, 1, strlen($this->slapNick));
                self::$messageStack .= $dottedNick .' : ';
                self::$messageStack .= $this->event->getArgument(1);
                $this->counter--; //one got
                if($this->counter == 0)
                {
                    $this->startListening = false;
                }
            }
        }
//        var_dump($this->usersPlugin->getUsers($this->event->getSource()));
    }
    /**
     * checks where from is coming event and reacts
     *  
     */
    public function onTick() {
        if($this->slap) $this->slap($this->channelTo);
        
        if(strlen(self::$messageStack) != 0 && $this->getConnection()->getHost() == $this->hostTo) 
        {
            $this->doPrivmsg($this->channelTo,self::$messageStack);
            self::$messageStack = '';
            if($this->counter == 0) $this->counter = $this->getConfig ('copycommand.counter');
        }
    }
    
    private function slap($channel) {
        $message = $this->getConnection()->getNick() .' slaps '. implode(' ',$this->usersPlugin->getUsers($channel)) .' with a tiny fish!';
        $this->doPrivmsg($channel,$message);
        $this->slap = false; //slap finished
    }
    
    private function hasSufficientPrivileges($nick,$channel) {
        if(     $this->usersPlugin->isOp($nick,$channel) || 
                $this->usersPlugin->isOwner($nick,$channel) || 
                $this->usersPlugin->isVoice($nick,$channel) || 
                $this->usersPlugin->isHalfop($nick,$channel))
        {
            return true;
        }
        return false;
    }
}

?>
