<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Helper\GlobalHelper;
use Auth;
use Validator;
use Image;
use Session;
use File;
use App\User;
use App\Chat;

class ChatController extends Controller
{
  
    public function chat()
    {
        return view('common.chat.chat');
    }

    public function index()
    {
        // Get all users except current logged in
        $users = Chat::where('user_id1', auth()->user()->id)
                    ->orWhere('user_id2',auth()->user()->id)
                    ->with('userOneData','userTwoData')
                    ->orderBy('updated_at','desc')
                    ->get();
        return view('common.chat.chatList', compact('users'));
    }

    public function usersChat($id)
    {
        $userRole = User::where('id',$id)
                        ->where('user_status','!=','-1')
                        ->first();
        if($userRole){
            $result = User::where('id',$id)->first();
            if($result){
                $receptorUser = User::where('id', '=', $id)->first();
                if($receptorUser == null) {
                    abort(404);
                    // return view('common.chat.nousernamefinded', compact('userName'));
                }else {
                    // $users = User::where('id', '!=', Auth::user()->id)->take(10)->get();
                    $users = Chat::where(function ($query) {
                                $query->where('user_id1', Auth::user()->id);
                            })
                            ->orWhere(function ($query) {
                                $query->where('user_id2', Auth::user()->id);
                            })->with('userOneData','userTwoData')
                            ->orderBy('updated_at','desc')
                            ->get();
                    $chat = $this->hasChatWith($receptorUser->id);
                    return view('common.chat.chat', compact('receptorUser', 'chat', 'users'));
                }
            }else{
                return redirect()->back();
            }
        }else{
            abort(404);
        }
    }

    public function hasChatWith($userId)
    {
        $chat = Chat::where('user_id1', Auth::user()->id)
            ->where('user_id2', $userId)
            ->orWhere('user_id1', $userId)
            ->where('user_id2', Auth::user()->id)
            ->get();
        if(!$chat->isEmpty()){
          $updateChat = Chat::find($chat[0]['id']);
          if(Auth::user()->id == $updateChat->user_id1){
            $updateChat->user_id1_read="2";
            $updateChat->user_id1_unread_count = '0';
          }
          if(Auth::user()->id == $updateChat->user_id2){
            $updateChat->user_id2_read="2";
            $updateChat->user_id2_unread_count = '0';
          }
          $updateChat->save();
          return $chat->first();
        }else{
            return $this->createChat(Auth::user()->id, $userId);;
        }
    }

    public function createChat($userId1, $userId2)
    {
        $chat = Chat::create([
            'user_id1' => $userId1,
            'user_id2' => $userId2
        ]);
        return $chat;
    }

    public function updateMessageMysql()
    {
        $apidata = request();
        $updateChat = Chat::find($apidata['chatId']);
        $updateChat->message=$apidata['textmessage']?$apidata['textmessage']:NULL;
        if($apidata['userId'] == $updateChat->user_id1){
          $updateChat->user_id1_read= "2";
          $updateChat->user_id2_read= "1";
          $updateChat->user_id2_unread_count = ($updateChat['user_id2_unread_count'] + 1);
        }else{
          $updateChat->user_id1_read="1";
          $updateChat->user_id2_read="2";
          $updateChat->user_id1_unread_count = ($updateChat['user_id1_unread_count'] + 1);
        }
        $updateChat->updated_at=date('Y-m-d H:i:s');
        $updateChat->save();

        // $user_id1 = User::where('id',$updateChat->user_id1)->first();
        // $user_id2 = User::where('id',$updateChat->user_id2)->whereNotNull('device_token')->first();
        // if($user_id1 && $user_id2){
        //     $title = $user_id1->full_name;
        //     $message = $updateChat->message?$updateChat->message:NULL;
        //     if($user_id2->device_token){
        //         $notification_type = '1';
        //         $chatDetail = Chat::where('id',$updateChat->id)->with('doctorDetail')->first();
        //         if($user_id2->device_type == 1){ // IOS
        //             $app_type = $user_id2->device_app_type?$user_id2->device_app_type:env('APP_TYPE');
        //             GlobalHelper::sendGCM($title, $message,$user_id2->device_token,$app_type,$notification_type,$chatDetail);
        //         }elseif($user_id2->device_type == 2){ // Android
        //             GlobalHelper::sendFCM($title, $message,$user_id2->device_token,$notification_type,$chatDetail);
        //         }
        //     }
        // }
    }

    public function adminUsersChat($id){
        
        $receptorUser = User::where('id', '=', $id)->first();
        if($receptorUser == null) {
            return view('common.chat.chat', compact('userName'));
        }else {
            $users = User::where('id', '!=', Auth::user()->id)->take(10)->get();
            $chat = $this->hasChatWith($receptorUser->id);
            return view('common.chat.chat', compact('receptorUser', 'chat', 'users'));
        }
    }

    //Mobile api
    public function chatList()
    {
        $apidata = request();
        $data = $apidata->json()->get('data');
        $users = Chat::where('user_id1',$data['user_id'])
                ->orWhere('user_id2',$data['user_id'])
                ->orderBy('updated_at', 'DESC')
                ->get();
        if($users){
            foreach ($users as $user) {
                if($data['user_id'] == $user['user_id1']){
                    $userDetail = User::find($user['user_id2']);
                    $user['user_id']=$user['user_id1'];
                    $user['patient_id']=$user['user_id2'];
                }else{
                    $userDetail = User::find($user['user_id1']);
                    $user['user_id']=$user['user_id2'];
                    $user['patient_id']=$user['user_id1'];
                }
                $user['profile_image']= isset($userDetail->profile_image)?url('public/uploads').'/'.$userDetail->profile_image:url('public/uploads/user-placeholder.jpg');
            }
            $response['status'] = 1;
            $response['data'] = $users;
            $response['message'] = "User chat list.";
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }else{
            $response['status'] = "0";
            $response['message'] = "No chat list.";
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }
    }

    //Mobile api
    public function createChatMobileapp()
    {
        $apidata = request();
        $data = $apidata->json()->get('data');
        $chat = Chat::create(['user_id1' => $data['user_id'],'user_id2' => $data['other_user_id']]);
        $response['status'] = 1;
        $response['data'] = $chat;
        $response['message'] = "Created chat.";
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }

    //Mobile api
    public function addLastmessage()
    {
        $apidata = request();
        $apidata = $apidata->json()->get('data');
        $updateChat = Chat::find($apidata['chatId']);
        $updateChat->message=$apidata['textmessage'];
        $updateChat->updated_at=date('Y-m-d H:i:s');
        $updateChat->save();

        $response['status'] = 1;
        $response['data'] = $updateChat;
        $response['message'] = "User chat list.";
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }

    public function store(Request $request)
    {
        if(!empty($request->image) || $request->image != ''){
            $file = $request->file('image');
            $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $file->getRealPath();
            $file->getSize();
            $file->getMimeType();
            $fileName = $request->mediaFileName;
            $path = base_path() . '/resources/uploads/chat_image/';
            if(!file_exists($path)) {
                File::makeDirectory($path, 0777, true);
                chmod($path,0777);
            }
            $upload = $request->file('image')->move(
                $path, $fileName
            );
            chmod($path.$fileName,0777);
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
            }
            return '1';
        }else{
            return '0';
        }
    }

    public function getAllUnreadMessage(){
        $results = Chat::
        where(function ($query) {
            $query->where('user_id1',auth()->user()->id)->where('user_id1_read','1');
        })
        ->orWhere(function ($query) {
            $query->where('user_id2',auth()->user()->id)->where('user_id2_read','1');
        })
        ->get();
        if($results->isEmpty()) {
            return 0;
        }else {
            $result = array();
            foreach($results as $value){
                $data['id'] = $value['id'];
                $data['user_id1'] = $value['user_id1'];
                $data['user_id2'] = $value['user_id2'];
                $data['message'] = $value['message'];
                $data['user_id1_read'] = $value['user_id1_read'];
                $data['user_id2_read'] = $value['user_id2_read'];
                $data['user_id1_unread_count'] = $value['user_id1_unread_count'];
                $data['user_id2_unread_count'] = $value['user_id2_unread_count'];
                $data['created_at'] = $value['created_at'];
                $data['updated_at'] = $value['updated_at'];
                if($value['user_id1'] == auth::user()->id){
                    $data['unread_message_count'] = $value['user_id1_unread_count'];
                }elseif($value['user_id2'] == auth::user()->id){
                    $data['unread_message_count'] = $value['user_id2_unread_count'];
                }
                $result[] = $data;
            }
            return $result;
        }
    }

    public static function getAllUnreadMessageCount(){
        $result = Chat::where('user_id1',auth()->user()->id)->where('user_id1_read','1')->count();
        if($result){
          return $result;
        }else{
          return 0;
        }
    }

    /**
    * Developed By : 
    * Description  : Upload Media
    * Date         :
    */
    public function updateMedia(Request $request)
    {
		$apidata = $request;
        if($apidata['mediaType'] == "Image" || $apidata['mediaType'] == "image"){
			if(!empty($apidata['mediaFile']) || $apidata['mediaFile'] != ''){
				$file = $apidata['mediaFile'];
				$fileExtension = $file->getClientOriginalExtension();
				$file->getRealPath();
				$file->getSize();
				$file->getMimeType();
				$fileName = md5(microtime(). $file->getClientOriginalName()).'.'.$fileExtension;
				$path = base_path() . '/public/uploads/chat_image/';
				if(!file_exists($path)) {
					File::makeDirectory($path, 0777, true);
					chmod($path,0777);
				}
				$upload = $apidata['mediaFile']->move(
					$path, $fileName
				);
				chmod($path.$fileName,0777);
				//   $imagePath = URL::asset('/public/uploads/chat_image').'/'.$fileName;
				$imagePath = $fileName;
				$fullpath = [
					"mediaPath" => $imagePath
				];
				return $this->APIResponse->respondWithMessageAndPayload($fullpath,'Data uploaded successfully.');
			}
        }elseif($apidata['mediaType'] == "Video" || $apidata['mediaType'] == "video"){
            if(!empty($apidata['mediaFile']) || $apidata['mediaFile'] != ''){
                $file = $apidata['mediaFile'];
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = md5(microtime(). $file->getClientOriginalName()).'.'.$fileExtension;
                $path = base_path() . '/public/uploads/chat_image/';
                if(!file_exists($path)) {
                    File::makeDirectory($path, 0777, true);
                    chmod($path,0777);
                }
                $upload = $apidata['mediaFile']->move(
                    $path, $fileName
                );
                chmod($path.$fileName,0777);

                $video_path = $path.$fileName;
                $thumbFileName = md5(microtime(). $file->getClientOriginalName());
                $thumb_fileName = $thumbFileName.'.jpg';
                $destinationPath = base_path() . '/public/uploads/chat_image/';
                $ffmpeg = '/usr/bin/ffmpeg';
                $thumbnail_path = $destinationPath.'/'.$thumb_fileName;
                $interval = 2;
                $size = '450x450';
                $cmd = $ffmpeg." -i $video_path -deinterlace -an -ss {$interval} -f mjpeg -t 1 -r 1 -y -s {$size} {$thumbnail_path} 2>&1";
                exec($cmd);
                // $videoPath = URL::asset('/public/uploads/chat_image').'/'.$fileName;
				// $thumbPath = URL::asset('/public/uploads/chat_image').'/'.$thumb_fileName;
				$videoPath = $fileName;
				$thumbPath = $thumb_fileName;
                $fullpath = [
                  "mediaPath" => $videoPath,
                  "thumbnailPath" => $thumbPath
                ];

                return $this->APIResponse->respondWithMessageAndPayload($fullpath,'Data uploaded successfully.');
            }
        }else{
			return $this->APIResponse->respondUnauthorized(__(trans('messages.common.mediaType')));
		}
    }

    public function countNotification()
    {
        $result = Chat::where(function ($query) {
                        $query->where('user_id1',auth()->user()->id)
                        ->where('user_id1_read','1');
                    })
                    ->orWhere(function ($query) {
                        $query->where('user_id2',auth()->user()->id)
                        ->where('user_id2_read','1');
                    })
                    ->count();
                    // dd($result);
        if($result) {
            return $result;
        }else {
            return 0;
        }
    }
}
