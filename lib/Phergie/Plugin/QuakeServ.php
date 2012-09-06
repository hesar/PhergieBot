<?php
/**
 *  @description plugin class for phergie to maintain Quakenet connection - based on quakeserv
 *  @author hesar 
 */
class Phergie_Plugin_QuakeServ extends Phergie_Plugin_Abstract
{
    /**
     * Nick of the quakeserv bot
     *
     * @var string
     */
    protected $botNick;

    /**
    * Identify message
    */
    protected $identifyMessage;

    /**
     * Checks for dependencies and required configuration settings.
     *
     * @return void
     */
    public function onLoad()
    {
        $this->getPluginHandler()->getPlugin('Command');

        $this->botNick = $this->getConfig('quakeserv.botnick', 'Q@CServe.quakenet.org');
        
    }

    /**
     * Checks for a notice from quakeserv and responds accordingly if it is an
     * authentication request or a notice that a ghost connection has been
     * killed.
     *
     * @return void
     */
    public function onNotice()
    {
            
    }

    /**
     * Checks to see if the original nick has quit; if so, take the name back.
     *
     * @return void
     */
    public function onQuit()
    {
        $eventNick = $this->event->getNick();
        $nick = $this->connection->getNick();
        if ($eventNick == $nick) {
            $this->doNick($nick);
        }
    }

    /**
     * Changes the in-memory configuration setting for the bot nick if it is
     * successfully changed.
     *
     * @return void
     */
    public function onNick()
    {
        $event = $this->event;
        $connection = $this->connection;
        if ($event->getNick() == $connection->getNick()) {
            $connection->setNick($event->getArgument(0));
        }
    }

    /**
     * Provides a command to terminate ghost connections.
     *
     * @return void
     */
    public function onCommandGhostbust()
    {
        $event = $this->event;
        $user = $event->getNick();
        $conn = $this->connection;
        $nick = $conn->getNick();

        if ($nick != $this->config['connections'][$conn->getHost()]['nick']) {
            $password = $this->config['quakeserv.password'];
            if (!empty($password)) {
                $this->doPrivmsg(
                    $this->event->getSource(),
                    $user . ': Attempting to ghost ' . $nick .'.'
                );
                $this->doPrivmsg(
                    $this->botNick,
                    'GHOST ' . $nick . ' ' . $password,
                    true
                );
            }
        }
    }
    public function onMode() {
        $password = $this->config['quakeserv.password'];
                    if (!empty($password)) {
                        $this->doPrivmsg($this->botNick,' AUTH '.  $this->connection->getUsername().' '. $this->config['quakeserv.password']);
                    }
                    unset($password);
                    $this->callJoin();
    }

    /**
     * Automatically send the GHOST command if the bot's nick is in use.
     *
     * @return void
     */
    public function onResponse()
    {
    }

    /**
     * Handle the server sending a KILL request.
     *
     * @return void
     */
    public function onKill()
    {
        $this->doQuit($this->event->getArgument(1));
    }


    private function callJoin() {
            $keys = null;
                if ($channels = $this->config['autojoin.channels']) {
                    if (is_array($channels)) {
                        // Support autojoin.channels being in these formats:
                        // 'hostname' => array('#channel1', '#channel2', ... )
                        $host = $this->getConnection()->getHost();
                        if (isset($channels[$host])) {
                            $channels = $channels[$host];
                        }
                        if (is_array($channels)) {
                            $keys = implode(',', $channels);
                            $channels = implode(',', array_keys($channels));
                        }
                    } elseif (strpos($channels, ' ') !== false) {
                        list($channels, $keys) = explode(' ', $channels);
                    }

                    $this->doJoin($channels, $keys);
                }
        }

}
    ?>
