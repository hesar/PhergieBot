<?php
/**
 *  @description plugin class for phergie to maintain Quakenet connection - based on NickServ
 *  @author hesar 
 */
class Phergie_Plugin_QuakeServ extends Phergie_Plugin_Abstract
{
    /**
     * Nick of the NickServ bot
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

        // Get the identify message
        $this->identifyMessage = $this->getConfig(
            'nickserv.identify_message',
            '/This nickname is registered./'
        );
        
        
    }

    /**
     * Checks for a notice from NickServ and responds accordingly if it is an
     * authentication request or a notice that a ghost connection has been
     * killed.
     *
     * @return void
     */
    public function onNotice()
    {
        $event = $this->event;
        if (strtolower($event->getNick()) == strtolower($this->botNick)) {
            $message = $event->getArgument(1);
            $nick = $this->connection->getNick();
            if (preg_match($this->identifyMessage, $message)) {
                $password = $this->config['quakeserv.password'];
                if (!empty($password)) {
                    $this->doPrivmsg($this->botNick,'AUTH',  $this->config['username'],  $this->config['quakeserv.password']);
                }
                unset($password);
            } elseif (preg_match('/^.*' . $nick . '.* has been killed/', $message)) {
                $this->doNick($nick);
            }
        }
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
            $password = $this->config['nickserv.password'];
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

    /**
     * Automatically send the GHOST command if the bot's nick is in use.
     *
     * @return void
     */
    public function onResponse()
    {
        if ($this->event->getCode() == Phergie_Event_Response::ERR_NICKNAMEINUSE) {
            $password = $this->config['nickserv.password'];
            if (!empty($password)) {
                $this->doPrivmsg(
                    $this->botNick,
                    'GHOST ' . $this->connection->getNick() . ' ' . $password
                );
            }
        }
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
}


?>
