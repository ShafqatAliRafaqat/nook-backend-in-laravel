<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\QB;

use App\Bookings;
use App\Notice;
use App\Shift;
use App\Visit;
use App\Complaint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Nook;

class APIDashboardController extends Controller {
    
    public function index() {
       
       $shared      = $this->shared();
       $independent = $this->independent();
       
       return [
           'shared' => $shared,
           'family' => $independent,
        ];
    }
    public function shared() {

        $qb = Nook::where('space_type', 'shared');
        
        $allBeds = DB::table('nooks as n')
                            ->join('rooms as r','r.nook_id','n.id')
                            ->where('n.space_type','shared')
                            ->sum('r.noOfBeds');

        $rentedBeds = Bookings::where('status', 'approved')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();
        
        // $rentedBeds = DB::table('nooks as n')
        //                     ->join('rooms as r','r.nook_id','n.id')
        //                     ->join('bookings as b','b.nook_id','n.id')
        //                     ->where('n.space_type','shared')
        //                     ->where('b.status','approved')
        //                     ->sum('r.noOfBeds');

        $vacantBeds = $allBeds - $rentedBeds;
        
        $pendingBooking = Bookings::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $inProgressBooking = Bookings::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $inProgressVisits = Visit::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $pendingVisits = Visit::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $pendingsComplaints = Complaint::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $inProgressComplaints = Complaint::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $pendingsNotice = Notice::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $inProgressNotice = Notice::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $pendingsShifts = Shift::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $inProgressShifts = Shift::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','shared');
        })->count();

        $payment = DB::table('nooks as n')
                            ->join('transactions as t','t.nook_id','n.id')
                            ->where('n.space_type','shared')
                            ->sum('t.amount');

        return [
            'allBeds'      => $allBeds,
            'rentedBeds'   => $rentedBeds,
            'vacantBeds'   => $vacantBeds,
            'pendingBooking'   => $pendingBooking,
            'inProgressBooking'   => $inProgressBooking,
            'pendingVisits'   => $pendingVisits,
            'inProgressVisits'   => $inProgressVisits,
            'pendingsComplaints'   => $pendingsComplaints,
            'inProgressComplaints'   => $inProgressComplaints,
            'pendingsNotice'   => $pendingsNotice,
            'inProgressNotice'   => $inProgressNotice,
            'pendingsShifts'   => $pendingsShifts,
            'inProgressShifts'   => $inProgressShifts,
            'payment'   => $payment,
        ];
    }
    public function independent() {

        $familyAllNooks = Nook::where('space_type', 'independent')->count();

        // $familyAllNooks = $qb->count();
        
        $familyRentedNooks = Bookings::where('status', 'approved')->whereHas('nook', function ($q) {
            $q->where('space_type', "independent");
        })->count();

        $familyvacantNooks = $familyAllNooks - $familyRentedNooks;
        
        $familyPendingNooks = Bookings::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyInProgressNooks = Bookings::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyInProgressVisits = Visit::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyPendingVisits = Visit::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyPendingsComplaints = Complaint::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyInProgressComplaints = Complaint::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyPendingsNotice = Notice::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyInProgressNotice = Notice::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyPendingsShifts = Shift::where('status', 'pending')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyInProgressShifts = Shift::where('status', 'in_progress')->whereHas('nook', function ($q) {
            $q->where('space_type','independent');
        })->count();

        $familyPayment = DB::table('nooks as n')
                            ->join('transactions as t','t.nook_id','n.id')
                            ->where('n.space_type','independent')
                            ->sum('t.amount');

        return [
            'allNooks'      => $familyAllNooks,
            'rentedNooks'   => $familyRentedNooks,
            'vacantNooks'   => $familyvacantNooks,
            'pendingNooks'   => $familyPendingNooks,
            'inProgressNooks'   => $familyInProgressNooks,
            'inProgressVisits'   => $familyInProgressVisits,
            'pendingVisits'   => $familyPendingVisits,
            'pendingsComplaints'   => $familyPendingsComplaints,
            'inProgressComplaints'   => $familyInProgressComplaints,
            'pendingsNotice'   => $familyPendingsNotice,
            'inProgressNotice'   => $familyInProgressNotice,
            'pendingsShifts'   => $familyPendingsShifts,
            'inProgressShifts'   => $familyInProgressShifts,
            'payment'   => $familyPayment,
        ];
    }
}
