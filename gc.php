<?php
class GC {
    private $calendarId;

    function __construct($calendarId)
    {
        $this->calendarId = $calendarId;
    }

    private function getService(){
        $client = new Google_Client();
        $client->setApplicationName('ISBOT115');
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setAuthConfig(__DIR__.'/tokens/g_credentials.json');
        $client->setAccessType('offline');
        $accessToken = json_decode(file_get_contents(__DIR__."/tokens/g_token.json"), true);
        $client->setAccessToken($accessToken);
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        return new Google_Service_Calendar($client);
    }

    function getEventsToday(){
        $service = $this->getService();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date("Y-m-d")."T07:00:00+03:00",
            'timeMax' => date("Y-m-d")."T21:00:00+03:00",
        );
        $events = $service->events->listEvents($this->calendarId, $optParams)->getItems();

        if (empty($events)) {
            return "Занятий сегодня нет <br>";
        } else {
            $stringRet = "Пары на завтра <br><br>";
            foreach ($events as $event) {
                $dateTime["start"] = new DateTime($event->start->dateTime);
                $dateTime["end"] = new DateTime($event->end->dateTime);
                $timeStart = $dateTime["start"]->format("H:i");
                $timeEnd = $dateTime["end"]->format("H:i");
                $stringRet .= "📕 $event->summary <br>🚪 Кабинет $event->location <br>🕔 $timeStart - $timeEnd <br><br>";
            }
            return $stringRet;
        }
    }

    function getEventsTomorrow(){
        $service = $this->getService();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date("Y-m-").(date("d")+1)."T07:00:00+03:00",
            'timeMax' => date("Y-m-").(date("d")+1)."T21:00:00+03:00",
        );
        $events = $service->events->listEvents($this->calendarId, $optParams)->getItems();

        if (empty($events)) {
            return "Занятий завтра нет <br>";
        } else {
            $stringRet = "Пары на завтра <br><br>";
            foreach ($events as $event) {
                $dateTime["start"] = new DateTime($event->start->dateTime);
                $dateTime["end"] = new DateTime($event->end->dateTime);
                $timeStart = $dateTime["start"]->format("H:i");
                $timeEnd = $dateTime["end"]->format("H:i");
                $stringRet .= "📕 $event->summary <br>🚪 Кабинет $event->location <br>🕔 $timeStart - $timeEnd <br><br>";
            }
            return $stringRet;
        }
    }

    function getEventsThisWeek(){
        $service = $this->getService();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('Y-m-d',strtotime("mon this week"))."T07:00:00+03:00",
            'timeMax' => date('Y-m-d',strtotime("sun this week"))."T21:00:00+03:00",
        );
        $events = $service->events->listEvents($this->calendarId, $optParams)->getItems();

        if (empty($events)) {
            return "Занятий на этой неделе нет<br>";
        } else {
            $days = array("<br>▶ ПОНЕДЕЛЬНИК ◀<br>", "<br>▶ ВТОРНИК ◀<br>", "<br>▶ СРЕДА ◀<br>", "<br>▶ ЧЕТВЕРГ ◀<br>", "<br>▶ ПЯТНИЦА ◀<br>", "<br>▶ СУББОТА ◀<br>", "<br>▶ ВОСКРЕСЕНЬЕ ◀<br>");
            foreach ($events as $event) {
                $dateTime["start"] = new DateTime($event->start->dateTime);
                $dateTime["end"] = new DateTime($event->end->dateTime);
                $date = $dateTime["start"]->format("d.m.Y");
                $timeStart = $dateTime["start"]->format("H:i");
                $timeEnd = $dateTime["end"]->format("H:i");
                $stringEvent = "📕 $event->summary <br>🚪 Кабинет $event->location <br>🕔 $timeStart - $timeEnd <br><br>";
                switch ($date){
                    case date("d.m.Y", strtotime("mon this week")):
                        $days[0] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("tue this week")):
                        $days[1] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("wed this week")):
                        $days[2] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("thu this week")):
                        $days[3] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("fri this week")):
                        $days[4] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("sat this week")):
                        $days[5] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("sun this week")):
                        $days[6] .= $stringEvent;
                        break;
                }
            }
            $stringRet = "ПАРЫ НА ЭТОЙ НЕДЕЛЕ<br>";
            foreach ($days as $day){
                if(strlen($day) < 50) {
                    $stringRet .= "$day Занятий нет 😊<br>";
                } else{
                    $stringRet .= $day;
                }
            }
            return $stringRet;
        }
    }

    function getEventsNextWeek(){
        $service = $this->getService();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('Y-m-d',strtotime("mon next week"))."T07:00:00+03:00",
            'timeMax' => date('Y-m-d',strtotime("sun next week"))."T21:00:00+03:00",
        );
        $events = $service->events->listEvents($this->calendarId, $optParams)->getItems();

        if (empty($events)) {
            return "Занятий на следующей неделе нет<br>";
        } else {
            $days = array("<br>▶ ПОНЕДЕЛЬНИК ◀<br>", "<br>▶ ВТОРНИК ◀<br>", "<br>▶ СРЕДА ◀<br>", "<br>▶ ЧЕТВЕРГ ◀<br>", "<br>▶ ПЯТНИЦА ◀<br>", "<br>▶ СУББОТА ◀<br>", "<br>▶ ВОСКРЕСЕНЬЕ ◀<br>");
            foreach ($events as $event) {
                $dateTime["start"] = new DateTime($event->start->dateTime);
                $dateTime["end"] = new DateTime($event->end->dateTime);
                $date = $dateTime["start"]->format("d.m.Y");
                $timeStart = $dateTime["start"]->format("H:i");
                $timeEnd = $dateTime["end"]->format("H:i");
                $stringEvent = "📕 $event->summary <br>🚪 Кабинет $event->location <br>🕔 $timeStart - $timeEnd <br><br>";
                switch ($date){
                    case date("d.m.Y", strtotime("mon next week")):
                        $days[0] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("tue next week")):
                        $days[1] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("wed next week")):
                        $days[2] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("thu next week")):
                        $days[3] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("fri next week")):
                        $days[4] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("sat next week")):
                        $days[5] .= $stringEvent;
                        break;
                    case date("d.m.Y", strtotime("sun next week")):
                        $days[6] .= $stringEvent;
                        break;
                }
            }
            $stringRet = "ПАРЫ НА СЛЕДУЮЩЕЙ НЕДЕЛЕ<br>";
            foreach ($days as $day){
                if(strlen($day) < 50) {
                    $stringRet .= "$day Занятий нет 😊<br>";
                } else{
                    $stringRet .= $day;
                }
            }
            return $stringRet;
        }
    }

    function getEventsOfDate($date){
        $service = $this->getService();

        $optParams = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $date."T07:00:00+03:00",
            'timeMax' => $date."T21:00:00+03:00",
        );
        $events = $service->events->listEvents($this->calendarId, $optParams)->getItems();

        if (empty($events)) {
            return "Занятий в этот день нет <br>";
        } else {
            $stringRet = "Занятия ".date('d.m.Y', strtotime($date))."<br><br>";
            foreach ($events as $event) {
                $dateTime["start"] = new DateTime($event->start->dateTime);
                $dateTime["end"] = new DateTime($event->end->dateTime);
                $timeStart = $dateTime["start"]->format("H:i");
                $timeEnd = $dateTime["end"]->format("H:i");
                $stringRet .= "📕 $event->summary <br>🚪 Кабинет $event->location <br>🕔 $timeStart - $timeEnd <br><br>";
            }
            return $stringRet;
        }
    }

    function getNumThisWeek(){
        $week = date('W')-date('W', strtotime("1 september"))+1;
        if($week > 0 && $week < 19){
            if($week == 6 || $week == 12 || $week == 16){
                return "$week неделя - КОНТРОЛЬНАЯ";
            } elseif ($week == 5 || $week == 11 || $week == 15) {
                return "$week неделя<br>Следующая неделя КОНТРОЛЬНАЯ 😭";
            } else {
                return "$week неделя";
            }
        } else {
            return "Новый семестр ещё не начался";
        }
    }


}