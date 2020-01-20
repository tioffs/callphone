<?php

namespace callphone;

/**
 * Класс авторизация на сайте с помощью звонка
 * @version 1.0.0
 * @author tioffs <timlab.ru>
 */
class Call
{

    /** Api ключ sms.ru */
    private $key;
    /** URL api sms.ru */
    private $url = "https://sms.ru/callcheck/";
    /** TimeOut Curll */
    private $timeOut = 5;
    /** Коды ошибок */
    private $errorCode = [
        1 => 'Ошибка json_decode',
        202 => 'Номер телефона указан неверно',
        204 => 'Сервер не доступен',
        400 => 'Номер пока не подтвержден',
        402 => 'Истекло время, отведенное для проверки, либо неправильно указан check_id'
    ];

    /** статус проверки 401 - пользователь позвонил с указанного номера */
    public $check_status;
    /** статус выполнения 100 - запрос выполнен */
    public $status_code;
    /** Идентификатор авторизации */
    public $check_id;
    /** Номер телефона, на который должен позвонить пользователь со своего номера */
    public $call_phone;
    /** сообщение об ошибке из массива $errorCode */
    public $error;
    /** Номер телефона, на который должен позвонить пользователь со своего номера, в более красивом виде */
    public $call_phone_pretty;
    /** сообщение от sms.ru */
    private $check_status_text;
    /** Статус выполнения String = "OK" */
    private $status;

    /** Установка API_KEY из config callphone.api_key */
    public function __construct($app_key)
    {
        $this->key = $app_key;
    }

    /**
     * Возвращает номер телефона на который требуется позвонить для верификации номера и $id для проверки статуса
     * @example $call->phone("79095552211")->call_phone
     * @param string $phone formate 79095552211
     * @return object
     */
    public function phone(string $phone): object
    {
        if (!preg_match('/^((7)+([0-9]){10})$/i', $phone)) {
            $this->status_code = 202;
            $this->error = $this->errorCode[202];
            return $this;
        }
        $this->curlPHP('add', [
            'api_id' => $this->key,
            'phone' => $phone,
            'json' => 1
        ]);
        return $this;
    }

    /**
     * Проверка авторизации
     * @example `if ($call->check("201737-542")->check_status === 401) true`
     * @param string $check_id
     * @return object
     */
    public function check(string $check_id): object
    {
        $this->curlPHP('status', [
            'api_id' => $this->key,
            'check_id' => $check_id,
            'json' => 1
        ]);
        return $this;
    }

    private function curlPHP(string $method, array $param)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url . $method . '?' . http_build_query($param),
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->timeOut,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($httpCode !== 200) {
            $this->status_code = 204;
            return;
        }
        $json = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->status_code = 1;
        }
        foreach ($json as $k => $v) {
            $this->{$k} = $v;
        }
        if (array_key_exists($this->status_code, $this->errorCode)) {
            $this->error = $this->errorCode[$this->status_code];
        }
        if (array_key_exists($this->check_status, $this->errorCode)) {
            $this->error = $this->errorCode[$this->check_status];
        }
    }
}
