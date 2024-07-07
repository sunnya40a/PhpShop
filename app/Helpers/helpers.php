<?php

if (!function_exists('sanitizeSearchText')) {
  /**
   * Sanitize input text
   *
   * @param string $text
   * @return string
   */
  function sanitizeSearchText($text)
  {

    $sanitizedText = preg_replace('/[^\s\w\-()!,.@[\]]/', '', $text);
    $sanitizedText = preg_replace('/\s+/', ' ', $sanitizedText);

    return $sanitizedText;
  }
}
