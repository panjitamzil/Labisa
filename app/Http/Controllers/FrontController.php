<?php

namespace App\Http\Controllers;

use Auth;
use App\Event;
use App\LogDonation;
use Illuminate\Http\Request;
use App\Components\AlertClass;
use App\Http\Requests\StoreDonationRequest;

class FrontController extends Controller
{
	const INC_DIR = 'pages.event._include.';
	
	public function index () {
    $limit_event = 9;
    $top_events = Event::orderBy('created_at', 'DESC')->limit(6)->get();
    $last_event = Event::orderBy('created_at', 'DESC')->limit($limit_event)->get();
		$sub_title = 'Indonesia Fundraising Platform';

    return view('pages.home.index', compact('sub_title', 'last_event', 'limit_event', 'top_events'));
  }
  
  public function eventList (Request $request) {
    $sub_title = 'Indonesia Fundraising Platform';
    $root_event = Event::whereNotNull('id');
    $category_event = 'Semua Kategori';

    if ($request->has('q')) {
      $events = $root_event->where('name', 'like', '%'.$request->q.'%')->orderBy('created_at', 'DESC')->paginate(9);
      $total_event = $root_event->where('name', 'like', '%'.$request->q.'%')->count();
    } else {
      $events = $root_event->orderBy('created_at', 'DESC')->paginate(9);
      $total_event = $root_event->count();
    }

		return view('pages.event.browse.index', compact('sub_title', 'events', 'total_event', 'category_event'));
  }

  public function eventView (Request $request) { 
    $event = Event::where('slug', $request->event_slug)->first();

    if (isset($event->id)) {
      $sub_title = $event->name;
      return view('pages.event.view.index', compact('sub_title', 'event'));  
    }

    return abort(404);
  }

  public function eventDonation (Request $request) {
    $INC_DIR = self::INC_DIR;
    $sub_title = 'Donasikan bantuan anda';
    $event = Event::where('slug', $request->event_slug)->first();

    if (isset($event->id)) {
      return view('pages.donation.index', compact('sub_title', 'INC_DIR', 'event'));
    }

    return abort(404);
  }

  public function eventDonationStore (StoreDonationRequest $request) {
    $event = Event::find($request->event_id);
 
    $alertClass = new AlertClass;
    $alertClass->makeAlert('danger', 'Terjadi kesalahan, mohon coba kembali!');

    $data = $request->all(); 
    if (isset($event->id)) {
      $data['event_id'] = $event->id;
      $query = LogDonation::create($data);

      if ($query) {
        $alertClass->makeAlert('success', 'Donasi berhasil ditambahkan atas nama ' . $request->donatur_name);
        return redirect()->route('event.view',['event_slug' => $event->slug])->with('success', 'Donate is success');   
      }
    }

    return redirect()->back();
  }
}
