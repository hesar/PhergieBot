<?php
require 'Phergie/GoogleApiLib/apiClient.php';
require 'Phergie/GoogleApiLib/contrib/apiCalendarService.php';
/*
 * This plugin catches !sms command and ads event to correct google calendar
 */
class Phergie_Plugin_GoogleCalendar extends Phergie_Plugin_Abstract
{
    private $access_token = 'ya29.AHES6ZSjR_E521etEqOxVyTVIvYLUwQOTmZA2JknXsnyV9Z4E7IphA';
    private $refresh_token = '1/uDfgGh-gfDljFj3nqdzeAOTAGFMoflaoEM72nnMjF4k';
    private $calendars; //array of calendars from config - googlecalendars

    
    public function onLoad() {
        $plugins = $this->getPluginHandler();
        $plugins->getPlugin('Command');
        $this->calendars = $this->getConfig('googlecalendars');
    }
    
    public function onCommandSms2() {
        $args = $this->event->getArguments();
        $this->createEvent($this->calendars['sms2'] ,str_replace('!sms2 ', '', $args[1]));
    }
    public function onCommandSms3() {
        $args = $this->event->getArguments();
        $this->createEvent($this->calendars['sms3'] ,str_replace('!sms3 ', '', $args[1]));
    }
    public function onCommandSms4() {
        $args = $this->event->getArguments();
        $this->createEvent($this->calendars['sms4'] ,str_replace('!sms4 ', '', $args[1]));
    }
    
    private function createEvent($calendar, $message) {
        $apiClient = new apiClient();
        $at =   '{"access_token":"' . $this->access_token . '",' .
                '"token_type":"Bearer",' .
                '"expires_in":3600,' .
                '"refresh_token":"' . $this->refresh_token . '",'.
                '"created":' . time() . '}';

        $apiClient->setAccessToken($at);
        $apiClient->refreshToken($this->refresh_token);
        $apiClient->setUseObjects(true);
        $service = new apiCalendarService($apiClient);
        $event = new Event();
        
        $event->setSummary('Zbiórka');
        $event->setLocation('Kanał IRC');
        $start = $end = new EventDateTime();
        $start->setDateTime('2012-09-20T15:00:00.000');
        $end->setDateTime('2012-09-20T16:00:00.000');
        $start->setTimeZone('Europe/Warsaw');
        $end->setTimeZone('Europe/Warsaw');
        $event->setStart($start);
        $event->setEnd($end);
        echo("\n wersja " . $service->version);
        $createdEvent = $service->events->quickAdd(
                $calendar,
                $message
                );
        $this->doPrivmsg($this->event->getSource(),'Utworzono wydarzenie : '.$createdEvent->created );
        
    }
}
?>
