<?php

class Sms
{
    protected array $data;

    public function __construct()
    {
        $this->initBase();
        $this->auth();
        $this->init();
    }

    protected function initBase()
    {
        $this->data['api_type'] = 'php';
        $this->data['api_path'] = 'https://api.smsbox.fr/api.';

        $this->data['mode'] = "Expert";
        $this->data['udh'] = 1;
        $this->data['origine'] = 'ASSURINCO';
        $this->data['strategy'] = 2;
        $this->data['sms_send'] = 0;
        $this->data['replace'] = [];
    }

    protected function auth()
    {
        $this->data['login'] = 'idonis';
        $this->data['password'] = md5("y3ueuTwM3s4S3aL4auBQ");
        $this->data['apikey'] = 'pub-e51ee0f134ed73e52e2196d0e2e6db22';
    }

    protected function init()
    {
        date_default_timezone_set('Europe/Paris');
        $date = date('d/m/Y H:i:s');

        $message = "ALERTE !!!" . br() . "The server is down at " . $date;
        $this->setMessage($message);
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    public function __set(string $name, string $value)
    {
        $this->data[$name] = $value;
    }

    public function setMessage(string $message)
    {
        if ($message == "") {
            return false;
        }

        $this->__set('message', $message);

        return true;
    }

    protected function numberValidator(string $number = '')
    {
        if (preg_match("/[0-9]{10}", $number)) {
            return true;
        } else {
            return false;
        }
    }

    protected function getFinaleMessage(mixed $data)
    {
        $message = $this->__get('message');

        foreach ($this->data['replace'] as $variable) {
            $message = str_replace('%' . $variable . '%', $data[$variable], $message);
        }

        return $message;
    }

    protected function sendSms(string $destinataire, string $message = '', bool $debug = false)
    {
        if ($destinataire == "") {
            return false;
        }

        $date = new DateTime();
        $url  = $this->__get('api_path') . $this->__get('api_type');

        if ($this->__get('apikey')) {
            $query = [
                'apiKey' => rawurldecode($this->__get('apiKey')),
                'destinataire' => $destinataire,
                'mode' => rawurlencode($this->__get('mode')),
                'origine' => rawurlencode($this->__get('origine')),
                'strategy' => rawurlencode($this->__get('strategy')),
                'message' => utf8_decode($message),
                'udh' => $this->__get('udh'),
                'date' => rawurlencode($date->format('d/m/Y')),
                'heure' => rawurlencode($date->format('H/i/s'))
            ];
        } else {
            $query = [
                'login' => rawurlencode($this->__get('login')),
                'password' => rawurlencode($this->__get('password')),
                'destinataire' => $destinataire,
                'mode' => rawurlencode($this->__get('mode')),
                'origine' => rawurlencode($this->__get('origine')),
                'strategy' => rawurlencode($this->__get('strategy')),
                'message' => utf8_decode($message),
                'udh' => $this->__get('udh'),
                'date' => rawurlencode($date->format('d/m/Y')),
                'heure' => rawurlencode($date->format('H/i/s'))
            ];
        }

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($query),
            ],
        ];

        if ($debug || $this->debug) {
            $this->data['tab_sms_send'][$destinataire] = 'Test Ok';
        } else {
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $this->data['tab_sms_send'][$destinataire] = $result;
        }

        return $result;
    }

    public function sendAlerteMessage(string $destinataire, string $other_message, bool $debug = false)
    {
        if (!empty($destinataire) && strlen($destinataire) == 10) {
            $message = $this->getFinaleMessage($destinataire) . " CODE : " . $other_message;

            if ($debug) {
                br($message);
                die("STOP DEBUG MESSAGE - 222");
            } else {
                if ($this->numberValidator($destinataire)) {
                    $this->sendSms($destinataire, $message);
                    $this->data['sms_send']++;
                } else {
                    $destinataire['MSG'] = $message;
                    $this->data['tab_sms_send'][] = $destinataire;
                }
            }
        }
    }
}
