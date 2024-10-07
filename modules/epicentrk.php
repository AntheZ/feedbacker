<?php

if (!defined('ABSPATH')) exit;

class Epicentrk_Module {
    public function process_url($url) {
        $html = $this->fetch_url($url);
        if (!$html) {
            return "Не вдалося отримати дані зі сторінки.";
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $title = $xpath->query('//h1[@class="p-title"]')->item(0)->nodeValue ?? '';
        $description = $xpath->query('//div[@class="p-block__text"]')->item(0)->nodeValue ?? '';
        $characteristics = $xpath->query('//div[@class="p-characteristics__item"]');

        $char_text = "";
        foreach ($characteristics as $char) {
            $char_text .= trim($char->nodeValue) . "\n";
        }

        $result = "Назва: " . trim($title) . "\n\n";
        $result .= "Опис: " . trim($description) . "\n\n";
        $result .= "Характеристики:\n" . trim($char_text);

        return $result;
    }

    private function fetch_url($url) {
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }
        return wp_remote_retrieve_body($response);
    }
}
