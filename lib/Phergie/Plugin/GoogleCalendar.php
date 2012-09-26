<?php
require(dirname(__FILE__).'./../GoogleApiLib/apiClient.php');
require(dirname(__FILE__).'/../GoogleApiLib/contrib/apiCalendarService.php');

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
    public function onCommandSms() {
        //some debugging
        $this->createEvent('',$this->event->getRawData());
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
    
    private function createEvent($calendar, $message = ' zbiorka ! ') {
        $apiClient = new apiClient();
        $time = time();
        $at =   '{"access_token":"' . $this->access_token . '",' .
                '"token_type":"Bearer",' .
                '"expires_in":3600,' .
                '"refresh_token":"' . $this->refresh_token . '",'.
                '"created":' . $time . '}';

        $apiClient->setAccessToken($at);
        $apiClient->refreshToken($this->refresh_token);
        $apiClient->setUseObjects(true);
        $service = new apiCalendarService($apiClient);
        $event = new Event();
        $event->setSummary($message);
        $event->setLocation('Kanał IRC');
        $start = $end = new EventDateTime();
        $start->setDateTime(date(DateTime::RFC3339,$time));
//        $start->setDateTime('2012-09-22T16:20:16.000Z');
        $end->setDateTime(date(DateTime::RFC3339,$time));
//        $end->setDateTime('2012-09-22T16:20:16.000Z');
        $start->setTimeZone('Europe/Warsaw');
        $end->setTimeZone('Europe/Warsaw');
        $event->setStart($start);
        $event->setEnd($end);
        
        $eventCreator = new EventCreator();
        $eventCreator->setEmail($calendar);
        $eventCreator->setDisplayName($calendar);
        $event->setCreator($eventCreator);
        
        $reminder = new EventReminder();
        $reminder->setMethod('sms');
        $reminder->setMinutes(0);

        $reminders = new EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides(array($reminder));
        
        $event->setReminders($reminders);
        $createdEvent = $service->events->insert(
                $calendar,  //TODO - set $calendar variable
                $event,
                array('sendNotifications' => true)
                );
//        $createdEvent = $service->events->quickAdd(
//                'hesar1975@gmail.com',
//                'wiadomosc testowa metoda QuickAdd',
//                array('sendNotifications' => true)
//                );
//        var_dump($createdEvent);
        $this->doPrivmsg($this->event->getSource(),'Utworzono wydarzenie : '.$createdEvent->created );
        
    }
    
}
?>
