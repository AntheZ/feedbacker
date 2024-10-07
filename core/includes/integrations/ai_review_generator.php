<?php

if (!defined('ABSPATH')) exit;

class AI_Review_Generator {
    private $api_key;

    public function __construct() {
        $this->api_key = get_option('chatgpt_api_key');
    }

    public function generate_review($product_info) {
        $prompt = $this->create_prompt($product_info);
        $response = $this->call_chatgpt_api($prompt);
        return $this->process_response($response);
    }

    private function create_prompt($product_info) {
        $title = isset($product_info['title']) ? $product_info['title'] : 'Невідомий продукт';
        $characteristics = isset($product_info['characteristics']) ? $product_info['characteristics'] : 'Характеристики відсутні';
        $description = isset($product_info['description']) ? $product_info['description'] : 'Опис відсутній';

        $prompt = "Створи позитивний відгук про цей товар: " . $title . "\n";
        $prompt .= "Характеристики:\n" . $characteristics . "\n";
        $prompt .= "Опис:\n" . $description . "\n";
        $prompt .= "Відгук має бути максимально близьким до людського, позитивного спрямування, згадати ключові характеристики товару.";
        return $prompt;
    }

    private function call_chatgpt_api($prompt) {
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ];
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates product reviews.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function process_response($response) {
        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        } elseif (isset($response['error'])) {
            return 'Помилка API: ' . $response['error']['message'];
        }
        return 'Не вдалося згенерувати відгук.';
    }
}
