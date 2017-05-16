<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\EditVideoRequest;
use App\Video;
use App\User;
use Config;
use File;
use YoutubeDl\YoutubeDl;
use FFMpeg\FFMpeg;
use Carbon\Carbon;
use Input;
use Response;

class VideosController extends Controller
{

    private $downloadPath = '/Users/michaelisakov/Projects/clipgur/public/video';

    public function index() {
      echo 'hello';
    }

    public function getUsers(){
      $users=User::all();
        return response()
            ->json([
                'success' =>true,
                'users'=>$users,
            ]);

    }

  public function uploadLocalVideo(){
      $file = Input::file('video');
        if($file) {
          $ext = $file->getClientOriginalExtension();
          $allowedexts = [
                      "3g2",
                      "3gp",
                      "aaf",
                      "asf",
                      "avchd",
                      "avi",
                      "drc",
                      "flv",
                      "m2v",
                      "m4p",
                      "m4v",
                      "mkv",
                      "mng",
                      "mov",
                      "mp2",
                      "mp4",
                      "mpe",
                      "mpeg",
                      "mpg",
                      "mpv",
                      "mxf",
                      "nsv",
                      "ogg",
                      "ogv",
                      "qt",
                      "rm",
                      "rmvb",
                      "roq",
                      "svi",
                      "vob",
                      "webm",
                      "wmv",
                      "yuv"
                    ];
          // if(!in_array( $ext, $allowedexts ) ){
          //     return Response::json(array(
          //         'success'=>false,
          //         'status'=>400,
          //         'message' =>'Allowed Only video files'
          //         )
          //       );
          // }
          $destinationPath = public_path() . '/video/';
          $fileName = time() . '_'.md5(strtolower($file->getClientOriginalName())).'.'.$ext;
          $is_uploaded = $file->move($destinationPath, $fileName);
          if ($is_uploaded) {
                return Response::json(array(
                  'success'=>true,
                  'status'=>200,
                  'path'=>$destinationPath.$fileName,
                  'message' =>'video uploaded'
                  )
                );

          } else {
            return Response::json(array(
                  'success'=>false,
                  'status'=>500,
                  'message' =>'something wrong happened while uploading your profile pic'
                  )
                );
          }
      }

    }

    public function edit(EditVideoRequest $request) {
      $src = $request['src'];
      $start = $request['start'];
      $duration = $request['duration'];

      $this->download($src);
      $this->editVideo($src, $start, $duration);
    }

    public function uploadToAmazon() {
      echo "hello";
      // echo $this->downloadPath . '/' . 'jx-UAI0nix81491690200.webm';
    }

    private function download($id) {
      echo "hello";

      $dl = new YoutubeDl([
          'continue' => true,
          'format' => 'webm',
          'id' => true,
          'write-all-thumbnails' => true
      ]);

      $dl->setDownloadPath($this->downloadPath);

      try {
          $video = $dl->download($id);
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

    private function editVideo($src, $start, $duration) {
      $video = $this->downloadPath . '/' . $src . '.webm';
      $this->edited = $this->downloadPath . '/' . $src . Carbon::now()->timestamp . '.webm';

      $ffmpeg = FFMpeg::create(array( 'ffmpeg.binaries' => '/usr/bin/ffmpeg', 'ffprobe.binaries' => '/usr/bin/ffprobe', 'timeout' => 1200, 'ffmpeg.threads' => 12));
      $video = $ffmpeg->open($video);
      $format = new \FFMpeg\Format\Video\WebM();

      $video->filters()->clip(\FFMpeg\Coordinate\TimeCode::fromSeconds($start), \FFMpeg\Coordinate\TimeCode::fromSeconds($duration));
      $video->save($format, $this->edited);

      $this->deleteFiles($src);
    }

    private function deleteFiles($src) {
      $video = $this->downloadPath . '/' . $src . '.webm';
      $photo = $this->downloadPath . '/' . $src . '.jpg';
      File::delete($video);
      File::delete($photo);
    }

}
