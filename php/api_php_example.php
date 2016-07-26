<?php
define('AUTH_TOKEN', '');  // Your authorization token
define('HOST', 'my.prom.ua');  // e.g.: my.prom.ua, my.tiu.ru, my.satu.kz, my.deal.by, my.prom.md


class EvoExampleClient {

    function EvoExampleClient($token) {
        $this->token = $token;
    }

    function make_request($method, $url, $body) {
        $headers = array (
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . HOST . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    function get_order_list() {
        $url = '/api/v1/orders/list';
        $method = 'GET';

        $response = $this->make_request($method, $url, NULL);

        return $response;
    }

    function get_order_by_id($id) {
        $url = '/api/v1/orders/' . $id;
        $method = 'GET';

        $response = $this->make_request($method, $url, NULL);

        return $response;
    }

    function set_order_status($ids, $status, $cancellation_reason, $cancellation_text) {
        $url = '/api/v1/orders/set_status';
        $method = 'POST';

        $body = array (
             'status'=> $status,
             'ids'=> $ids
        );

        $response = $this->make_request($method, $url, $body);

        return $response;
    }
}


if (empty(AUTH_TOKEN)) {
    throw new Exception('Sorry, there\'s no any AUTH_TOKEN');
}

$client = new EvoExampleClient(AUTH_TOKEN);

$order_list = $client->get_order_list();
if (empty($order_list['orders'])) {
    throw new Exception('Sorry, there\'s no any order');
}
// echo var_dump($order_list);

$order_id = $order_list['orders'][0]['id'];

$order = $client->get_order_by_id($order_id);
// echo var_dump($order);

$set_status_result = $client->set_order_status((array) $order_id, 'received', NULL, NULL);
// echo var_dump($set_status_result);

$order = $client->get_order_by_id($order_id);
// echo var_dump($order);

?>
