<?php

namespace App\Console\Commands;

use App\Helpers\GeoCoder;
use App\Order;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DispatchOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch order to online delivery boys';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct() {
        parent::__construct();
    }

    public function handle(){

        $this->info('Dispatching Orders...');

        $host = env('SOCKET_IO_HOST','http://localhost:3000');
        $users = json_decode(file_get_contents("$host/api/user/active"));

        $minutes = Setting::getValue('order_dispatch_before_pickupTime'); // 10 min

        $time = Carbon::now()->addMinutes($minutes)->toDateTimeString();
        $orders = Order::where('status',Order::$ACCEPTED)->where('pickup_time','>=',$time)->get();

        $usersOrders = [];
        $distance = [];
        foreach ($users as $user){
            foreach ($orders as $order){
                $distance[md5($order->id)] = GeoCoder::distanceFromLatLng([
                    'iLat' => $order->addressLocation->lat,
                    'iLng' => $order->addressLocation->lng,
                    'lat' => $user->lng,
                    'lng' => $user->lng,
                ]);
            }
            $usersOrders[] =  ['user_id'=>$user->id,'order'=>$distance];
        }

        $dispatchQue = [];

        foreach ($usersOrders as $usersOrder){
            $key = array_keys($usersOrder['order'], min($usersOrder['order']));
            $dispatchQue[$key] = $usersOrder['user_id'];
        }

        foreach ($orders as $order){
            $user_id = $dispatchQue[md5($order->id)];
            $order->update([
                'status' => Order::$READY_TO_PICKUP,
                'deliveryBoy_id' => $user_id
            ]);
        }

        $this->info(count($orders).' orders dispatched to delivery boys.');

    }
}
