<?php

return array(

    // One array per connection, pretty self-explanatory
    'connections' => array(
        // Ex: All connection info for the Freenode network
        array(
            'host' => 'irc.rizon.net',
            'port' => 6666,
            'username' => 'BlueBoss',
            'realname' => 'BlueBoss',
            'nick' => 'BlueBoss'
            // 'password' => 'Blueroserzadzi',
            // 'transport' => 'ssl', // uncomment to connect using SSL
            // 'encoding' => 'UTF-8', // uncomment if using UTF-8
            // 'context' => array('socket' => array('bindto' => '0.0.0.0:0')), // uncomment to force use of IPv4
        ),
        array(
            'host' => 'irc.quakenet.org',
            'port' => 6667,
            'username' => 'bluerose',
            'realname' => 'bluerose',
            'nick' => 'bluerose'
        )
    ),

    'processor' => 'async',
    'processor.options' => array('usec' => 1000000),
    // Time zone. See: http://www.php.net/manual/en/timezones.php
    'timezone' => 'UTC',
    'debug' => true,
    // Whitelist of plugins to load
    'plugins' => array(
        // To enable a plugin, simply add a string to this array containing
        // the short name of the plugin as shown below.

        // 'ShortPluginName',

        // Below is an example of enabling the AutoJoin plugin, for which
        // the corresponding PEAR package is Phergie_Plugin_AutoJoin. This
        // plugin allows you to set a list of channels in this configuration
        // file that the bot will automatically join when it connects to a
        // server. If you'd like to enable this plugin, simply install it,
        // uncomment the line below, and set a value for the setting
        // autojoin.channels (examples for which are located further down in
        // this file).

        // 'AutoJoin',

        // A few other recommended plugins:

        // Servers randomly send PING events to clients to ensure that
        // they're still connected and will eventually terminate the

        // connection if a PONG response is not received. The Pong plugin
        // handles sending these responses.

        // 'Pong',

        // It's sometimes difficult to distinguish between a lack of
        // activity on a server and the client not receiving data even
        // though a connection remains open. The Ping plugin performs a self
        // CTCP PING sporadically to ensure that its connection is still
        // functioning and, if not, terminates the bot.

        // 'Ping',

        // Sometimes it's desirable to have the bot disconnect gracefully
        // when issued a command to do so via a PRIVMSG event. The Quit
        // plugin implements this using the Command plugin to intercept the
        // command.

        // 'Quit',
        'Ping',
        'Pong',
//        'AutoJoin',
        'NickServ',
        'QuakeServ',
        'Command',
        'CopyCommand',
        'UserInfo',
        'Remind',
        'GoogleCalendar'
    ),

    // If set to true, this allows any plugin dependencies for plugins
    // listed in the 'plugins' option to be loaded even if they are not
    // explicitly included in that list
    'plugins.autoload' => true,

    // Enables shell output describing bot events via Phergie_Ui_Console
    'ui.enabled' => true,

    // Examples of a prefix for command-based plugins
    'command.prefix' => '.',
    // If you uncomment the line above, this would invoke onCommandJoin 
    // in the Join plugin: !join #channel
    // By default, no prefix is assumed, so the same command would be 
    // invoked like this: join #channel
    
    // Examples of supported values for autojoins.channel:
    // 'autojoin.channels' => '#channel1,#channel2',
    // 'autojoin.channels' => array('#channel1', '#channel2'),
    // 'autojoin.channels' => array(
    //                            'host1' => '#channel1,#channel2',
    //                            'host2' => array('#channel3', '#channel4')
    //                        ),

    // Examples of setting values for Ping plugin settings

    // This is the amount of time in seconds that the Ping plugin will wait
    // to receive an event from the server before it initiates a self-ping

    // 'ping.event' => 300, // 5 minutes

    // This is the amount of time in seconds that the Ping plugin will wait
    // following a self-ping attempt before it assumes that a response will
    // never be received and terminates the connection

    // 'ping.ping' => 10, // 10 seconds
    'nickserv.password' => 'Blueroserzadzi',
    'quakeserv.password' => 'ave666',
    'autojoin.channels' => array(
        'irc.quakenet.org' => array('#dowodztwo.erepublik.pl' => 'mechanicznapomarancza'), //array('#channel' => 'channel key')
        'irc.rizon.net' => array('#bluerose.bicie')
    ),
    // define slap copy channels and networks
    'copycommand.channels' => array(
        'channelFrom' => array('irc.quakenet.org' , '#dowodztwo.erepublik.pl'),
        'channelTo' => array('irc.rizon.net' , '#bluerose.bicie')
    ),
    /*
     * array of users that are able to op themselves - BY nick
     */
    'copycommand.users' => array("hesar"),
    'copycommand.counter' => 1, //how many lines to copy after slap (ba carefull with this one)
    'googlecalendars' => array(
        'sms2' => 'diw2.br@gmail.com',
        'sms3' => 'diw3.br@gmail.com',
        'sms4' => 'diw4.br@gmail.com',
    )
);
