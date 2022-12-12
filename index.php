<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__.'/gc.php';

class ServerHandler extends VK\CallbackApi\Server\VKCallbackApiServerHandler {
    function confirmation(int $group_id, ?string $secret) {
        $vk_token = json_decode(file_get_contents(__DIR__.'/tokens/vk_token.json'), true);
        if ($secret === $vk_token['secret'] && $group_id === $vk_token['group_id']) {
            echo $vk_token['confirmation_token'];
        }
    }

    private function parseDate($str_date){
        $parse_date = preg_split("/ /iu",trim($str_date));
        if(preg_match("/года/iu", $parse_date[count($parse_date)-1])) {
            $pos = count($parse_date)-3;
            $date[0] = $parse_date[count($parse_date)-4];
            $date[2] = $parse_date[count($parse_date)-2];
        } else {
            $pos = count($parse_date)-1;
            $date[0] = $parse_date[count($parse_date)-2];
            $date[2] = date("Y");
        }
        switch (true){
            case (preg_match("/^сентябр[яь]$/iu", $parse_date[$pos])):
                $date[1] = "09";
                break;
            case (preg_match("/^октябр[яь]$/iu", $parse_date[$pos])):
                $date[1] = "10";
                break;
            case (preg_match("/^ноябр[яь]$/iu", $parse_date[$pos])):
                $date[1] = "11";
                break;
            case (preg_match("/^декабр[яь]$/iu", $parse_date[$pos])):
                $date[1] = "12";
                break;
            case (preg_match("/^январ[яь]$/iu", $parse_date[$pos])):
                $date[1] = "01";
                break;
            case (preg_match("/^феврал[яь]$/iu", $parse_date[$pos])):
                $date[1] = "02";
                break;
            case (preg_match("/^март(а|)$/iu", $parse_date[$pos])):
                $date[1] = "03";
                break;
            case (preg_match("/^апрел[яь]$/iu", $parse_date[$pos])):
                $date[1] = "04";
                break;
            case (preg_match("/^ма[яй]$/iu", $parse_date[$pos])):
                $date[1] = "05";
                break;
            case (preg_match("/^июн[яь]$/iu", $parse_date[$pos])):
                $date[1] = "06";
                break;
            case (preg_match("/^июл[яь]$/iu", $parse_date[$pos])):
                $date[1] = "07";
                break;
            case (preg_match("/^август(а|)$/iu", $parse_date[$pos])):
                $date[1] = "08";
                break;
            default:
                $date[0] = date("d");
                $date[1] = date("m");
                $date[2] = date("Y");

        }
        return "$date[2]-$date[1]-$date[0]";
    }

    function messageNew(int $group_id, ?string $secret, array $object) {
        echo "ok";
        $arr = [
            'one_time' => false,
            'buttons' => [
                [
                    [
                        'action' => [
                            'type' => 'text',
                            'payload' => "{\"button\": \"1\"}",
                            'label' => 'На сегодня 😉'
                        ],
                        'color' => 'positive'
                    ],
                    [
                        'action' => [
                            'type' => 'text',
                            'payload' => "{\"button\": \"2\"}",
                            'label' => 'На завтра 🤞'
                        ],
                        'color' => 'positive'
                    ]
                ],
                [
                    [
                        'action' => [
                            'type' => 'text',
                            'payload' => "{\"button\": \"3\"}",
                            'label' => 'На эту неделю ✨'
                        ],
                        'color' => 'primary'
                    ],
                    [
                        'action' => [
                            'type' => 'text',
                            'payload' => "{\"button\": \"4\"}",
                            'label' => 'На следующую неделю 🤔'
                        ],
                        'color' => 'primary'
                    ]
                ],
                [
                    [
                        'action' => [
                            'type' => 'text',
                            'payload' => "{\"button\": \"5\"}",
                            'label' => 'Какая эта неделя?🖖'
                        ],
                        'color' => 'negative'
                    ]
                ]
            ]
        ];
        $vk_token = json_decode(file_get_contents(__DIR__.'/tokens/vk_token.json'), true);
        $gc = new GC($vk_token['calendar_id']);
        $regex = json_decode(file_get_contents(__DIR__.'/phrases/bot_phrases.json'), true);
        $message = array(
           'peer_id' => $object['peer_id'],
           'random_id' => rand(100, 1000000),
            'keyboard' => json_encode($arr, JSON_UNESCAPED_UNICODE),
        );
        switch (true){
            case (preg_match($regex['hello'],$object['text'])):
                $hello_phrase = json_decode(file_get_contents(__DIR__."/phrases/hello.json"));
                $message['message'] = $hello_phrase[rand(0, count($hello_phrase)-1)];
                break;
            case (preg_match($regex['thanks'],$object['text'])):
                $thanks_phrase = json_decode(file_get_contents(__DIR__."/phrases/thanks.json"));
                $message['message'] = $thanks_phrase[rand(0, count($thanks_phrase)-1)];
                break;
            case (preg_match($regex['smile'],$object['text'])):
                $smile_phrase = json_decode(file_get_contents(__DIR__."/phrases/smiles_phrases.json"));
                $message['sticker_id'] = $smile_phrase[rand(0, count($smile_phrase)-1)];
                break;
            case (preg_match($regex['gEsTod'],$object['text'])):
                $message['message'] = $gc->getEventsToday();
                break;
            case (preg_match($regex['gEsTom'],$object['text'])):
                $message['message'] = $gc->getEventsTomorrow();
                break;
            case (preg_match($regex['gEsTW'],$object['text'])):
                $message['message'] = $gc->getEventsThisWeek();
                break;
            case (preg_match($regex['gEsNW'],$object['text'])):
                $message['message'] = $gc->getEventsNextWeek();
                break;
            case (preg_match($regex['gEoD'], $object['text'])):
                $message['message'] = $gc->getEventsOfDate($this->parseDate($object['text']));
                break;
            case (preg_match($regex['gNTW'],$object['text'])):
                $message['message'] = $gc->getNumThisWeek();
                break;
            case (preg_match($regex['help'], $object['text'])):
                $message['message'] = "Хммм, никогда не задумывался над этим вопросом. Давай я чуть позже отвечу 😊";
                break;
            default:
                $message['message'] = "Мне непонятно, что Вы хотите 😒";
        }
        $vk = new VK\Client\VKApiClient("5.85");
        $vk->messages()->send($vk_token['access_token'], $message);
    }

}

$handler = new ServerHandler();
$data = json_decode(file_get_contents('php://input'));
$handler->parse($data);
