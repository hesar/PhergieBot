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
      private $zbiorka_trwa = false;
      private $notification_send = false;
      public static $messageStackFull = array('Brak rozkazow/No orders ');

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
          $this->getPluginHandler()->getPlugin('Command');
      }
      
      /**
       * gets action from $channelFrom (slap), checks if it is @ or + and sets flag for slap whole
       * $channelTo in next tick
       * also sets flag for startListening for true 
       */
      public function onAction() {
//          var_dump(strpos($this->event->getArgument(1),'bluerose'));
//          var_dump(strpos($this->event->getArgument(1),'bluerose') != false);
          if($this->getConnection()->getHost() == $this->hostFrom && $this->event->getSource() == $this->channelFrom && $this->hasSufficientPrivileges($this->event->getNick(),$this->event->getSource()) && strpos($this->event->getArgument(1),'bluerose') !== false)
          {
              $this->startListening = true;
              $this->slap = true;
              $this->slapNick = $this->event->getNick();
              $this->counter = $this->getConfig('copycommand.counter');
              if(self::$messageStackFull[0] == 'Brak rozkazow/No orders ') self::$messageStackFull[0] = 'Rozkazy :';
              $this->zbiorka_trwa = true;
          }
    }
    
    public function onResponse() {    }
    
    public function onJoin() {
            if($this->zbiorka_trwa && $this->getConnection()->getHost() == $this->hostTo) 
                {
                    $this->doPrivmsg($this->event->getSource(), $this->event->getNick().' trwa zbiorka - szczegolowe info komenda: .bicie ');
                }
        }

    /**
     * 1. check if request comes from correct channel 
     * 2. check if it is message from the user that slapped (we KNOW that it is a user with sufficient privileges
     * 3. push message and decrement counter
     */
    public function onPrivmsg() {
//        var_dump(strpos($this->event->getArgument(1),'.hl'));
        if($this->getConnection()->getHost() == $this->hostFrom && $this->event->getSource() == $this->channelFrom && $this->hasSufficientPrivileges($this->event->getNick(),$this->event->getSource()) && strpos($this->event->getArgument(1),'.hl') !== false)
          {
              $this->startListening = true;
              $this->slap = true;
              $this->slapNick = $this->event->getNick();
              $this->counter = $this->getConfig('copycommand.counter');
              if(self::$messageStackFull[0] == 'Brak rozkazow/No orders ') self::$messageStackFull[0] = 'Rozkazy :';
              $this->zbiorka_trwa = true;
              return;
          }
          
        if($this->startListening && $this->getConnection()->getHost() == $this->hostFrom && $this->counter != 0) {
            if($this->getEvent()->getNick() == $this->slapNick) {
                $dottedNick = substr($this->slapNick, 0, 1).'.'.substr($this->slapNick, 1, strlen($this->slapNick));
                self::$messageStack .= $dottedNick .' : ';
                self::$messageStack .= $this->event->getArgument(1);
                if($this->zbiorka_trwa) {
                    date_default_timezone_set('Europe/Warsaw');
                    array_push(self::$messageStackFull, date (DateTime::ISO8601, time())." : ".$this->event->getArgument(1));
                }
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
        if($this->connection->getHost() == $this->hostTo && $this->zbiorka_trwa && $this->notification_send == false) {
            $this->doPrivmsg($this->channelTo,'Urochomilem procedure zbiorki: .bicie start');
            $this->notification_send = true;
        }
        
        if(strlen(self::$messageStack) != 0 && $this->getConnection()->getHost() == $this->hostTo) 
        {
            $this->doPrivmsg($this->channelTo,self::$messageStack);
            self::$messageStack = '';
            if($this->counter == 0) $this->counter = $this->getConfig ('copycommand.counter');
        }
    }
    
    public function onCommandHl() {
        if($this->hasSufficientPrivileges($this->event->getNick(), $this->event->getSource())) 
        {
            $this->slap($this->event->getSource());
        }
    }
    public function onCommandOp() {
        $users = $this->getConfig('copycommand.users');
        if(in_array( $this->event->getNick(), $users)) $this->doMode($this->event->getSource(),'o',$this->event->getNick());
    }
    public function onCommandBicie() {
        
            $what_to_do  = $this->event->getArgument(1);
            switch ($what_to_do) {
                case '.bicie start':
                    if($this->hasSufficientPrivileges($this->event->getNick(), $this->event->getSource())) {
                        $this->zbiorka_trwa = true;
                    }
                    break;
                case '.bicie stop':
                    if($this->hasSufficientPrivileges($this->event->getNick(), $this->event->getSource())) 
                    {
                        $this->zbiorka_trwa = false;
                        $this->notification_send = false;
                        self::$messageStackFull = array('Brak rozkazow/No orders ');
                        $this->doPrivmsg($this->channelTo,'Zamknalem procedure zbiorki.');
                    }
                    break;
                case '.bicie':
                    foreach (self::$messageStackFull as $key => $line) {
                        $this->doPrivmsg($this->event->getNick(),  $line);
                    }
                    break;
                default:
                    break;
            }
            if(strpos($what_to_do,'.bicie add') !== false && $this->hasSufficientPrivileges($this->event->getNick(), $this->event->getSource())) {
                $str = str_replace('.bicie add', '', $what_to_do);
                if(self::$messageStackFull[0] == 'Brak rozkazow/No orders ') self::$messageStackFull[0] = 'Rozkazy :';
                date_default_timezone_set('Europe/Warsaw');
                array_push(self::$messageStackFull, date(DateTime::ISO8601, time())." : ".$str);
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
