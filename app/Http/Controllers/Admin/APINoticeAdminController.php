<?php

namespace App\Http\Controllers\Admin;

use App\Bookings;
use App\Complaint;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\NoticeResource;
use App\LatLng;
use App\Nook;
use App\Notice;
use App\Room;
use App\Transaction;
use App\User;
use App\UserDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;

class APINoticeAdminController extends BaseController
{

    protected $permissions = [
        'index' => 'notice-list-all',
    ];

    public function index(Request $request){
        
        $input = $request->all();

        $qb = Notice::orderBy('updated_at', 'DESC')->with(['user', 'nook']);

        $qb = QB::where($input, "id", $qb);
        $qb = QB::where($input, "status", $qb);
        $qb = QB::whereLike($input, "details", $qb);
        $qb = QB::where($input, "user_id", $qb);
        $qb = QB::where($input, "nook_id", $qb);


        $qb = $qb->whereHas('nook', function ($q) use ($request) {
            if ($request->space_type) {
                $q->where('space_type', $request->space_type);
            }
            if ($request->nookCode) {
                $q->where('nookCode', $request->nookCode);
            }
        });

        $qb = $qb->whereHas('user', function ($q) use ($request) {
            if ($request->number) {
                $q->where('number', $request->number);
            }
            if ($request->email) {
                $q->where('email', $request->email);
            }
        });

        $notices = $qb->paginate(20);

        $notices->appends(Input::except('page'));

        return NoticeResource::collection($notices);
    }

    public function migrate()
    {
        // $users = User::get();
        // foreach($users as $user){
        //     if($user->number && strlen($user->number) == 10){
        //         $user->update([
        //             'number' => '0' . $user->number,
        //         ]);
        //     }
        // }

        // $users = User::get();
        // return $users;
    }

    // public function migrate(){
    //     $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
    //     $json = file_get_contents($storagePath.'/old_nook.json');
    //     $tables = json_decode($json,true);

    //     $users = [];
    //     $nooks = [];

    //     foreach ($tables as $table){
    //         if($table['type']  == 'table' && $table['name'] == 'addresses'){
    //             foreach ($table['data'] as $address){
    //                 if($address['user_id']){
    //                     $users[$address['user_id']] =  $address;
    //                 }
    //                 if($address['nook_id']){
    //                     $nooks[$address['nook_id']] =  $address;
    //                 }
    //             }
    //         }
    //         if($table['type']  == 'table' && $table['name'] == 'users'){
    //             foreach ($table['data'] as $user){
    //                 if(isset($users[$user['id']])){
    //                     $users[$user['id']] =  array_merge($users[$user['id']],$user);
    //                 }else{
    //                     $users[$user['id']] =  $user;
    //                 }
    //             }
    //         }

    //         $types = [
    //             'house' => 'House',
    //             'flat' => 'Flat',
    //             'independentRoom' => 'Independent Room',
    //             'hostelBuilding' => 'Hostel Building',
    //             'outHouse' => 'Out House',
    //             'other' => 'Other',
    //         ];

    //         $STATUS = [
    //             'pending' => 'pending',
    //             'inProgress' => 'inProgress',
    //             'active' => 'approved',
    //             'rejected' => 'rejected',
    //         ];

    //         if($table['type']  == 'table' && $table['name'] == 'nooks'){
    //             foreach ($table['data'] as $nook){
    //                 if(isset($nooks[$nook['id']])){
    //                     $nooks[$nook['id']] =  array_merge($nooks[$nook['id']],$nook);
    //                 }else{
    //                     $nooks[$nook['id']] =  $nook;
    //                 }
    //             }
    //         }

    //         if($table['type']  == 'table' && $table['name'] == 'room_details'){
    //             foreach ($table['data'] as $room){
    //                 Room::create([
    //                     'capacity' => $room['room_type'],
    //                     'noOfBeds' => $room['total_beds'],
    //                     'price_per_bed' => $room['price_per_bed'],
    //                     'nook_id' => $room['nook_id']
    //                 ]);
    //             }
    //         }

    //         $statuses = [
    //             'pending' => 'pending',
    //             'inprogress'=>'in_progress',
    //             'approved' => 'approved',
    //             'rejected' => 'rejected',
    //             'cancel' => 'rejected',
    //             'cancelled' => 'cancelled',
    //         ];

    //         $types = [
    //             'Internet' => 'internet',
    //             'Cleaning' => 'cleaning',
    //             'Entertainment' => 'entertainment',
    //             'Security' => 'security',
    //             'Food' => 'food',
    //             'Maintenance' => 'maintenance',
    //             'Discipline' => 'discipline',
    //             'Staff Related' => 'staff_related',
    //             'Privacy' => 'privacy',
    //             'other' => 'Other'
    //         ];

    //         if($table['type']  == 'table' && $table['name'] == 'complains'){
    //             foreach ($table['data'] as $complain){
    //                 Complaint::create([
    //                     'id' => $complain['id'],
    //                     'description' => $complain['description'],
    //                     'type' => $types[$complain['complain_type']],
    //                     'status' => $statuses[$complain['status']],
    //                     'user_id' => $complain['user_id'],
    //                     'nook_id' => $complain['nook_id'],
    //                 ]);
    //             }
    //         }

    //         if($table['type']  == 'table' && $table['name'] == 'notices'){
    //             foreach ($table['data'] as $notice){
    //                 Notice::create([
    //                     'id' => $notice['id'],
    //                     'details' => $notice['details'],
    //                     'status' => $statuses[$notice['status']],
    //                     'checkout' => Carbon::parse($notice['check_out_date']),
    //                     'user_id' => $notice['user_id'],
    //                     'nook_id' => $notice['nook_id'],
    //                 ]);
    //             }
    //         }

    //         if($table['type']  == 'table' && $table['name'] == 'payments'){
    //             foreach ($table['data'] as $payment){
    //                 Transaction::create([
    //                     'id' => $payment['id'],
    //                     'amount' => $payment['amount'],
    //                     'details' => '',
    //                     'status' => $statuses[$payment['status']],
    //                     'receipt_id' => 0,
    //                     'user_id' => $payment['user_id'],
    //                     'nook_id' => 0,
    //                 ]);
    //             }
    //         }


    //         if($table['type']  == 'table' && $table['name'] == 'my_nooks'){
    //             foreach ($table['data'] as $booking){
    //                 Bookings::create([
    //                     'id' => $booking['id'],
    //                     'status' => $statuses[$booking['status']],
    //                     'rent' => $booking['price_per_bed'],
    //                     'security' => 0,
    //                     'paidSecurity' => 0,
    //                     'user_id' => $booking['user_id'],
    //                     'nook_id' => $booking['nook_id'],
    //                 ]);
    //             }
    //         }

    //     }

    //     foreach ($users as $user){

    //         $number = isset($user['phone_number']) ? $user['phone_number']: '';

    //         if(isset($user['phone_number'])){
    //             $number = str_replace (' ','',$number);
    //             $number = str_replace ('+92','',$number);
    //             $number = str_replace ('-','',$number);
    //         }

    //         $name = isset($user['username']) ? $user['username']: '';
    //         $gender = isset($user['gender']) ? $user['gender']: 'male';
    //         $country = isset($user['country']) ? $user['country']: '';
    //         $city = isset($user['city']) ? $user['city']: '';
    //         $state = isset($user['state']) ? $user['state']: '';
    //         $zip_code = isset($user['zip_code']) ? $user['zip_code']: '';

    //         User::create([
    //             'id' => $user['id'],
    //             'name' => $name,
    //             'number' => $number,
    //             'email' => $user['email'],
    //             'password' => $user['encrypted_password'],
    //         ]);

    //         UserDetails::create([
    //             'user_id' => $user['id'],
    //             'address' => $city. ' '. $state. ' '. $zip_code. ' '.$country,
    //             'gender' => $gender,
    //             'cnic' => isset($user['cnic']) ? $user['cnic']: null,
    //             'occupation' => isset($user['occupation']) ? $user['occupation']: null,
    //             'imei' => isset($user['imei']) ? $user['imei']: null,
    //             'nook_id' => isset($user['nook_id']) ? $user['nook_id']: 0,
    //             'numberVerified' => 1,
    //         ]);
    //     }

    //     foreach ($nooks as $nook){
    //         $latlng = LatLng::create([
    //             'lat' => $nook['lat'],
    //             'lng' => $nook['lng']
    //         ]);
    //         Nook::create([
    //             'id' => $nook['id'],
    //             'type' => 'house',
    //             'space_type' => 'shared',
    //             'gender_type' => $nook['nook_gender'],
    //             'status' => $STATUS[$nook['status']],
    //             'nookCode' => $nook['code'],
    //             'description' => $nook['description'],
    //             'facilities' => json_encode([
    //                 'TV', 'AC','Wifi','Furniture','Kitchen','Kitchen Accessories',
    //                 'Electronic Iron','Gas Bill','Water Bill','Parking','Transport',
    //                 'Oven','Cable','Laundry','Food','Fridge','Security Guard','CCTV',
    //                 'Water Filter','UPS','Lounge','Hot Water','House Keeping','Generator'
    //             ]),
    //             'video_url' => 'https://www.youtube.com/watch?v='.$nook['video_url'],
    //             'number' => $nook['phone_number'],
    //             'country' => $nook['country'],
    //             'state' => $nook['state'],
    //             'city' => $nook['city'],
    //             'zipCode' => $nook['zip_code'],
    //             'address' => $nook['location'],
    //             'latLng_id' => $latlng->id,
    //             'partner_id' => 1
    //         ]);
    //     }

    //     return [
    //         'message' => 'Users Added Successfully'
    //     ];
    // }

    public function edit(Request $request, $id)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            abort(400, $validator->errors()->first());
        }

        $notice = Notice::where('id', $id)->first();

        if (!$notice) {
            abort(404, 'Notice not found.');
        }

        if($input['status'] != $notice->status){
            NotificationsHelper::SEND([
                'title' => 'Notice Updated',
                'body' => 'Notice status updated to ' . $input['status'],
            ],$notice->user_id, env("APP_ID"));
        }
        
        $notice->update([
            'status' => $input['status']
        ]);

        return [
            'message' => 'Notice Updated Successfully',
            'notice' => NoticeResource::make($notice),
        ];
    }
}
