<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CreateMediaRequest;
use App\Http\Requests\EditMediaRequest;
use App\Http\Resources\MediaAdminResource;
use App\Media;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;

class APIMediaAdminController extends BaseController {

    protected $permissions = [
//        'index' => 'media-list',
//        'show' => 'media-show',
//        'create' => 'media-create',
//        'edit' => 'media-edit',
//        'delete' => 'media-delete',
    ];

    public function index(Request $request){

        $input = $request->all();

        $qb  = Media::orderBy('updated_at','DESC');

        $qb = QB::where($input,"nook_id",$qb);
        $qb = QB::whereLike($input,"name",$qb);
        $qb = QB::whereLike($input,"caption",$qb);

        $medias = $qb->get();

        return MediaAdminResource::collection($medias);
    }

    public function show($id){

        $media = $this->getMedia($id,"media-show-all");

        if(!$media){
            abort(400,__('messages.model.not.found',[
                'model'=>__('messages.media')
            ]));
        }

        return MediaAdminResource::make($media);
    }

    public function create(Request $request){

        $input = $request->all();

        $paths = $this->saveImages($request);

        $media = Media::create([
            'name' => $paths['name'],
            'caption' => $input['caption'],
            'alt' => $input['alt'],
            'nook_id' => $input['nook_id'],
            'path' => $paths['path'],
            'small' => $paths['small'],
            'medium' => $paths['medium'],
        ]);

        return [
            'message'=>__('messages.model.create.success',[
                'model'=>__('messages.media')
            ]),
            'data' => MediaAdminResource::make($media)
        ];
    }

    public function edit(Request $request,$id){


        $input = $request->all();

        $media = $this->getMedia($id,"media-edit-all");

        if(!$media){
            abort(400,__('messages.model.not.found',[
                'model'=>__('messages.media')
            ]));
        }

        $secureInput = [
            'name' => $input['name'],
            'caption' => $input['caption'],
            'alt' => $input['alt'],
            'nook_id' => $input['nook_id'],
        ];

        if($request->image){
            $this->deleteImages($media);
            $paths = $this->saveImages($request);
            $secureInput = array_merge($secureInput,$paths);
        }

        $media->update($secureInput);

        return [
            'message'=>__('messages.model.edit.success',[
                'model'=>__('messages.media')
            ]),
            'data' => MediaAdminResource::make($media)
        ];
    }

    public function delete($id){

        $media = $this->getMedia($id,"media-delete-all");

        if(!$media){
            abort(400,__('messages.model.not.found',[
                'model'=>__('messages.media')
            ]));
        }

        $this->deleteImages($media);

        $media->delete();

        return [
            'message'=>__('messages.model.delete.success',[
                'model'=>__('messages.media')
            ])
        ];

    }

    // private function

    private function saveImages(Request $request){

        $img = Image::make($request->image);

        $result = FileHelper::getAndCreatePath($img->filename,'medias');

        $extension = substr($img->mime,strpos($img->mime,'/')+1);

        $result['name'] = $result['name'].str_random(15).'.'.$extension;

        $path = $this->saveImage($img,'default',1,$result);
        $medium = $this->saveImage($img,'medium',0.75,$result);
        $small = $this->saveImage($img,'small',0.40,$result);

        return [
            'path' => $path,
            'medium' => $medium,
            'small' => $small,
            'name' => $result['name'],
        ];
    }

    private function saveImage($img,string $prefix, float $percentage,array $result){

        $img->resize($img->width()*$percentage, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $path = $result['path']."/$prefix-".$result['name'];
        $img->save($path, 60);

        return $path;
    }

    private function deleteImages($media){
        FileHelper::deleteFileIfNotDefault($media->small);
        FileHelper::deleteFileIfNotDefault($media->medium);
        FileHelper::deleteFileIfNotDefault($media->path);
    }

    private function getMedia($id,$p){
        return Media::where('id',$id)->first();
    }

}
