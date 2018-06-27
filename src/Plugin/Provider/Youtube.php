<?php

namespace Hunter\video_embed_youtube\Plugin\Provider;

use Hunter\video_embed\Plugin\ProviderPluginBase;
use Hunter\video_embed\Annotation\VideoEmbedProvider;

/**
 * @VideoEmbedProvider(
 *   id = "youtube",
 *   title = "Youtube"
 * )
 */
 class Youtube extends ProviderPluginBase {

  /**
   * {@inheritdoc}
   */
  public function renderEmbedCode($width, $height, $autoplay) {
    return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/'.$this->getVideoId().'?autoplay='.$autoplay.'&amp;start=0&amp;rel=0"></iframe>';
  }

  /**
   * Get the time index for when the given video starts.
   *
   * @return int
   *   The time index where the video should start based on the URL.
   */
  protected function getTimeIndex() {
    preg_match('/[&\?]t=(?<timeindex>\d+)/', $this->getInput(), $matches);
    return isset($matches['timeindex']) ? $matches['timeindex'] : 0;
  }

  /**
   * Extract the language preference from the URL for use in closed captioning.
   *
   * @return string|FALSE
   *   The language preference if one exists or FALSE if one could not be found.
   */
  protected function getLanguagePreference() {
    preg_match('/[&\?]hl=(?<language>[a-z\-]*)/', $this->getInput(), $matches);
    return isset($matches['language']) ? $matches['language'] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteThumbnailUrl() {
    $url = 'http://img.youtube.com/vi/%s/%s.jpg';
    $high_resolution = sprintf($url, $this->getVideoId(), 'maxresdefault');
    $backup = sprintf($url, $this->getVideoId(), 'mqdefault');
    try {
      $this->httpClient->head($high_resolution);
      return $high_resolution;
    }
    catch (\Exception $e) {
      return $backup;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getIdFromInput($input) {
    preg_match('/^https?:\/\/(www\.)?((?!.*list=)youtube\.com\/watch\?.*v=|youtu\.be\/)(?<id>[0-9A-Za-z_-]*)/', $input, $matches);
    return isset($matches['id']) ? $matches['id'] : FALSE;
  }

}
