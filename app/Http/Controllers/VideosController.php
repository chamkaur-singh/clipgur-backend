<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Video;

use YoutubeDl\YoutubeDl;
use FFMpeg\FFMpeg;

class VideosController extends Controller
{
    public function index() {
      $videos = Video::all();
      $this->format();
      // $this->download('xVUyQC7AE0Q');
      // $this->edit();
      // return $videos;
    }

    // public function edit($video, $start, $end) {
    public function edit() {
        $video = '/Users/michaelisakov/Projects/clipgur/public/video/xVUyQC7AE0Q.webm';
        $edited = '/Users/michaelisakov/Projects/clipgur/public/video/edited.webm';
        $percentage = 0;
        $start = 11;
        $end = 18;
        $ffmpeg = FFMpeg::create(array( 'ffmpeg.binaries' => '/usr/bin/ffmpeg', 'ffprobe.binaries' => '/usr/bin/ffprobe', 'timeout' => 1200, 'ffmpeg.threads' => 12));
        $video = $ffmpeg->open($video);
        $format = new \FFMpeg\Format\Video\WebM();
        $video->filters()->clip(\FFMpeg\Coordinate\TimeCode::fromSeconds($start), \FFMpeg\Coordinate\TimeCode::fromSeconds($end));
        $video->save($format, $edited);
    }

    public function format($format) {
      $format = 'wmv';
      $vid = '/Users/michaelisakov/Projects/clipgur/public/video/xVUyQC7AE0Q';
      $ffmpeg = FFMpeg::create(array( 'ffmpeg.binaries' => '/usr/bin/ffmpeg', 'ffprobe.binaries' => '/usr/bin/ffprobe', 'timeout' => 1200, 'ffmpeg.threads' => 12));
      $video = $ffmpeg->open($vid . '.webm');
      switch ($format) {
        case 'wmv':
          $video->save(new \FFMpeg\Format\Video\WMV(), $vid . '.wmv');
          break;
        case 'webm':
          $video->save(new FFMpeg\Format\Video\WebM(), $vid . '.webm');
          break;
        case 'mp4':
          $video->save(new \FFMpeg\Format\Video\X264(), $vid . '.mp4');
          break;
      }
    }

    // xVUyQC7AE0Q
    public function download($id) {
      $dl = new YoutubeDl([
          'continue' => true,
          'format' => 'webm',
          'id' => true,
          'write-all-thumbnails' => true,
      ]);

      $dl->setDownloadPath('/Users/michaelisakov/Projects/clipgur/public/video');

      try {
          $video = $dl->download($id);
          echo $video;
      } catch (NotFoundException $e) {
          echo "Video not found";
      } catch (PrivateVideoException $e) {
          echo "Video is private";
      } catch (CopyrightException $e) {
          echo "The YouTube account associated with this video has been terminated due to multiple third-party notifications of copyright infringement";
      } catch (\Exception $e) {
          echo "Failed to download";
      }
    }

    public function show(Video $video) {
      return $video;
    }
}
