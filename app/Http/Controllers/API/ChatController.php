<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Response\APIResponse;
use Validator;
use App\User;
use App\Chat;
use Auth;
use File;
use Image;
use URL;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Helper\GlobalHelper;
use Session;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->APIResponse = new APIResponse();
    }

    /**
    * Developed By : 
    * Description  : Get User Chat List
    * Date         :
    */
    public function getUserChatList(Request $request){
        try{
            $user = Chat::where('user_id1',auth::user()->id)
                    ->orWhere('user_id2',auth::user()->id)
                    ->with('userOneData','userTwoData')
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            if(!$user->isEmpty()){
                $output = array();
                foreach($user as $users){
                    $data['id'] = $users['id'];
                    $data['user_id1'] = $users['user_id1'];
                    $data['user_id2'] = $users['user_id2'];
                    $data['message'] = $users['message'];
                    $data['user_id1_read'] = $users['user_id1_read'];
                    $data['user_id2_read'] = $users['user_id2_read'];
                    $data['user_id1_unread_count'] = $users['user_id1_unread_count'];
                    $data['user_id2_unread_count'] = $users['user_id2_unread_count'];
                    $data['created_at'] = $users['created_at'];
                    $data['base_url'] = url('/resources/uploads/chat_image/').'/';
                    if($users['user_id1'] == auth::user()->id){
                        $data['user_two_data'] = $users['userOneData'];
                        $data['unread_message_count'] = $users['user_id1_unread_count'];
                    }elseif($users['user_id2'] == auth::user()->id){
                        $data['user_two_data'] = $users['userTwoData'];
                        $data['unread_message_count'] = $users['user_id2_unread_count'];
                    }
                    $output[] = $data;
                }
                // $mediaPathUrl = URL::asset('/resources/uploads/chat_image/').'/';
                return $this->APIResponse->respondWithMessageAndPayload(['user'=>$output],'Record found.');
            }else{
                return $this->APIResponse->respondNotFound('No record found.');
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By : 
    * Description  : Save last message in single chat
    * Date         :
    */
    public function saveLastMessage(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)) {
                return $this->APIResponse->respondNotFound('Pease pass key.');
            }else {
                $rules = array(
                    'id'=>'required|numeric',
                );
                $messages = [
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $findChat = Chat::where('id',$data['id'])->first();
                    if($findChat){
                        $findChat->message = isset($data['message'])?$data['message']:NULL;
                        $findChat->user_id1_read="1";
                        $findChat->user_id2_read="2";
                        $findChat->user_id1_unread_count = ($findChat['user_id1_unread_count'] + 1);
                        $findChat->save();
                        return $this->APIResponse->respondWithMessageAndPayload($findChat,'Record found');
                    }else{
                        return $this->APIResponse->respondNotFound('No record found.');
                    }
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By : 
    * Description  : mark as read chat message
    * Date         :
    */
    public function markReadMessage(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)) {
                return $this->APIResponse->respondNotFound('Please pass key.');
            }else {
                $rules = array(
                    'id'=>'required|numeric',
                );
                $messages = [
                    'id.required' => trans('messages.common.id_required'),
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $findChat = Chat::find($data['id']);
                    if($findChat){
                        $findChat->user_id1_read="2";
                        $findChat->user_id2_read="2";
                        $findChat->user_id1_unread_count = '0';
                        $findChat->user_id2_unread_count = '0';
                        $findChat->save();
                        return $this->APIResponse->respondWithMessageAndPayload($findChat,'Record found');
                    }else{
                        return $this->APIResponse->respondNotFound('No record found.');
                    }
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }

    /**
    * Developed By : 
    * Description  : Upload Media
    * Date         :
    */

    public function updateMedia(Request $request)
    {
        if(!empty($request['mediaFile']) || $request['mediaFile'] != ''){
            $file = $request->file('mediaFile');
            $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $file->getRealPath();
            $file->getSize();
            $file->getMimeType();
            $fileType = $file->getMimeType();
            $fileName = md5(microtime(). $file->getClientOriginalName()) . "." . $fileExtension;
            $path = base_path() . '/resources/uploads/chat_image/';
            if(!file_exists($path)) {
                File::makeDirectory($path, 0777, true);
                chmod($path,0777);
            }
            $upload = $request->file('mediaFile')->move(
                $path, $fileName
            );
            chmod($path.$fileName,0777);
            if(strstr($fileType, "video/")){
                $video_path = $path.$fileName;
                $thumbFileName = md5(microtime(). $file->getClientOriginalName());
                $thumb_fileName = $thumbFileName.'.jpg';
                $destinationPath = base_path() . '/resources/uploads/chat_image/';
                $ffmpeg = '/usr/bin/ffmpeg';
                $thumbnail_path = $destinationPath.'/'.$thumb_fileName;
                $interval = 2;
                $size = '450x450';
                $cmd = $ffmpeg." -i $video_path -deinterlace -an -ss {$interval} -f mjpeg -t 1 -r 1 -y -s {$size} {$thumbnail_path} 2>&1";
                exec($cmd);
                // $videoPath = URL::asset('/resources/uploads/chat_image').'/'.$fileName;
				// $thumbPath = URL::asset('/resources/uploads/chat_image').'/'.$thumb_fileName;
				$videoPath = $fileName;
				$thumbPath = $thumb_fileName;
                
            }
            $fullpath = [
                "mediaPath" => $fileName,
                "thumbnailPath" => isset($thumbPath)?$thumbPath:'',
                "base_url" => url('/resources/uploads/chat_image/').'/',
            ];
            return $this->APIResponse->respondWithMessageAndPayload($fullpath,"Media upload successfully ");
            // return $fullpath;
        }else{
            return $this->APIResponse->respondNotFound("Oops Faile to upload media.");
        }
    }

    public function updateMediaBK(Request $request)
    {
        $apidata = $request;
        if(!empty($apidata['mediaFile']) || $apidata['mediaFile'] != ''){
            $file = $apidata['mediaFile'];
            $fileExtension = $file->getClientOriginalExtension();
            $file->getRealPath();
            $file->getSize();
            $file->getMimeType();
            $fileName = md5(microtime(). $file->getClientOriginalName()).'.'.$fileExtension;
            $path = base_path() . '/resources/uploads/chat_image/';
            if(!file_exists($path)) {
                File::makeDirectory($path, 0777, true);
                chmod($path,0777);
            }
            $upload = $apidata['mediaFile']->move(
                $path, $fileName
            );
            chmod($path.$fileName,0777);
            //   $imagePath = URL::asset('/resources/uploads/chat_image').'/'.$fileName;
            if(strstr($file->getMimeType(), "video/")){
                $video_path = $path.$fileName;
                $thumbFileName = md5(microtime(). $file->getClientOriginalName());
                $thumb_fileName = $thumbFileName.'.jpg';
                $destinationPath = base_path() . '/resources/uploads/chat_image/';
                $ffmpeg = '/usr/bin/ffmpeg';
                $thumbnail_path = $destinationPath.'/'.$thumb_fileName;
                $interval = 2;
                $size = '450x450';
                $cmd = $ffmpeg." -i $video_path -deinterlace -an -ss {$interval} -f mjpeg -t 1 -r 1 -y -s {$size} {$thumbnail_path} 2>&1";
                exec($cmd);
                // $videoPath = URL::asset('/resources/uploads/chat_image').'/'.$fileName;
				// $thumbPath = URL::asset('/resources/uploads/chat_image').'/'.$thumb_fileName;
				$videoPath = $fileName;
				$thumbPath = $thumb_fileName;
                $fullpath = [
                  "mediaPath" => $videoPath,
                  "thumbnailPath" => $thumbPath
                ];
            }else if(strstr($file->getMimeType(), "image/")){
                $imagePath = $fileName;
                $fullpath = [
                    "mediaPath" => $imagePath
                ];
            }
            return $this->APIResponse->respondWithMessageAndPayload($fullpath,'Data uploaded successfully.');
        }
    }

    /**
    * Developed By :
    * Description  : Get Chat ID
    * Date         :
    */
    public function getChatID(Request $request){
        $data = $request->json()->get('data');
        try{
            if(empty($data)) {
                return $this->APIResponse->respondNotFound(__('Data key not found or Empty'));
            }else {
                $rules = array(
                    'user_id1'=>'required|numeric',
                    'user_id2'=>'required|numeric',
                );
                $messages = [
                    'user_id1.required' => 'Please enter User id 1',
                    'user_id2.required' => 'Please enter User id 2'
                ];
                $validator = Validator::make($data, $rules,$messages);
                if($validator->fails()) {
                    return $this->APIResponse->respondValidationError(__($validator->errors()->first()));
                }else{
                    $findChat = Chat::where('user_id1', $data['user_id1'])
                        ->where('user_id2', $data['user_id2'])
                        ->orWhere('user_id1', $data['user_id2'])
                        ->where('user_id2', $data['user_id1'])
                        ->first();
                    if($findChat){
                        $chat = $findChat;
                    }else{
                        $chat = Chat::create([
                            'user_id1' => $data['user_id1'],
                            'user_id2' => $data['user_id2'],
                        ]);
                    }
                    return $this->APIResponse->respondWithMessageAndPayload($chat,'Chat Record found');
                }
            }
        }catch (\Exception $e) {
            return $this->APIResponse->handleAndResponseException($e);
        }
    }
}