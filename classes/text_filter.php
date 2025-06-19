<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  Video Carousel filtering
 *
 *  This filter generate a carousel of videos
 *
 * @package    filter_mirocarousel
 * @copyright  2025 Samuel Calegari <samuel.calegari@univ-perp.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_mirocarousel;

defined('MOODLE_INTERNAL') || die();

class text_filter extends \core_filters\text_filter {

    public function filter($text, array $options = []) {

        global $PAGE;

        $PAGE->requires->js('/filter/mirocarousel/main.js');

        if (!is_string($text) or empty($text)) {
            return $text;
        }

        if (strpos($text, '[mirocarousel') === false) {
            return $text;
        }

        $text = preg_replace_callback(
            "/\[[^\[]*mirocarousel[^\]]*\]/",
            array( &$this, "gen_carousel" ),
            $text);


        return $text;
    }

    private function gen_carousel($m) {

        $html = '';

        $regex = '/(\w+)\s*=\s*"(.*?)"/';

        preg_match_all($regex, $m[0], $matches);

        $carouselinfos = array();
        for ($i = 0; $i < count($matches[1]); $i++)
            $carouselinfos[$matches[1][$i]] = $matches[2][$i];

        $videos = explode(",",$carouselinfos["videos"]);
        $titles = explode(",",$carouselinfos["titles"]);

        $nb = count($videos);

        $id = uniqid("mirocarousel-");


        $html .= '<div class="container-fluid px-0">';
        $html .= '<div id="'. $id .'" class="carousel slide carousel-video" data-ride="carousel" data-interval="false">';

        $html .= '<ol class="carousel-indicators">';
        for($i=0;$i<$nb;$i++)
            $html .= '  <li data-target="#carouselVideoIndicators" data-slide-to="'.$i.'" ' . ($i==0 ? 'class="active"' : '') . '></li>';
        $html .= ' </ol>';

        $html .= '<div class="carousel-inner bg-info" role="listbox">';

        for($i=0;$i<$nb;$i++) {

            $video = $videos[$i];

            $url = '';
            $iframeParameters = '';

            if(strpos($video, 'youtube-')!== false) {
                $url = '//www.youtube.com/embed/' . str_replace('youtube-', '', $video) . '?rel=0&enablejsapi=1&version=3&playerapiid=ytplayer';
            } elseif(strpos($video, 'dailymotion-')!== false) {
                $url = '//www.dailymotion.com/embed/video/' . str_replace('dailymotion-','',$video) . '?api=postMessage';
                $iframeParameters = 'api=1';
            } elseif(strpos($video, 'vimeo-')!== false) {
                $url = '//player.vimeo.com/video/' . str_replace('vimeo-', '', $video);
            } elseif(strpos($video, 'upvdstream-')!== false) {
                $url = 'https://upvdstream.univ-perp.fr/video/player.php?id=' . str_replace('upvdstream-', '', $video) . '&cover=cover2';
            } elseif(strpos($video, 'mediaserver-')!== false) {
                $url = 'https://mediaserver.univ-perp.fr/permalink/' . str_replace('mediaserver-', '', $video) . '/iframe/';
            }

            $html .= '    <div class="carousel-item ' . ($i==0 ? 'active' : '') . '">';
            $html .= '        <div class="d-flex justify-content-center min-vh-100">';
            $html .= '            <div class="embed-responsive embed-responsive-16by9">';
            $html .= '                <iframe class="embed-responsive-item miro_video_player_iframe" ' . $iframeParameters  . ' src="' . $url . '"></iframe>';
            $html .= '            </div>';
            $html .= '        </div>';
            $html .= '        <div class="carousel-caption d-none d-md-block">';
            $html .= '        <p>' . $titles[$i] . '</p>';
            $html .= '        </div>';
            $html .= '    </div>';
        }

        $html .= '</div>';

        $html .= '<a class="carousel-control-prev" href="#'. $id .'" role="button" data-slide="prev">';
        $html .= '    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2 shadow d-block" aria-hidden="true"></span>';
        $html .= '    <span class="sr-only">' . get_string('previous', 'filter_mirocarousel') . '</span>';
        $html .= '</a>';
        $html .= '<a class="carousel-control-next" href="#'. $id .'" role="button" data-slide="next">';
        $html .= '    <span class="carousel-control-next-icon bg-dark rounded-circle p-2 shadow d-block" aria-hidden="true"></span>';
        $html .= '    <span class="sr-only">' . get_string('next', 'filter_mirocarousel') . '</span>';
        $html .= '</a>';

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
