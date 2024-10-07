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

        $title = $xpath->query('//h1[@data-product-name]')->item(0)->nodeValue ?? '';

        $description = $this->get_section_content($xpath, 'Опис', $title);
        $characteristics = $this->get_section_content($xpath, 'Характеристики', $title);

        $result = "Назва: " . trim($title) . "\n\n";
        $result .= "Опис:\n" . trim($description) . "\n\n";
        $result .= "Характеристики:\n" . trim($characteristics);

        return $result;
    }

    private function get_section_content($xpath, $section_name, $product_name) {
        $query = "//div[contains(text(), '{$section_name} {$product_name}')]/ancestor::div[1]";
        $node = $xpath->query($query)->item(0);
        if ($node) {
            $content = '';
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    if ($child->tagName === 'p' || $child->tagName === 'span') {
                        $content .= $child->nodeValue . "\n\n";
                    } elseif ($child->tagName === 'ul') {
                        foreach ($child->childNodes as $li) {
                            if ($li->nodeType === XML_ELEMENT_NODE && $li->tagName === 'li') {
                                $content .= "- " . $li->nodeValue . "\n";
                            }
                        }
                        $content .= "\n";
                    } else {
                        $content .= $child->nodeValue . "\n";
                    }
                }
            }
            return trim($content);
        }
        return '';
    }

    private function fetch_url($url) {
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }
        return wp_remote_retrieve_body($response);
    }
}
