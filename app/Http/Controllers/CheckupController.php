<?php

namespace App\Http\Controllers;

use App\Models\Master;
use DB;
use Illuminate\Http\Request;

class CheckupController extends Controller
{
    private function lang($text)
    {
        if (session("langSelect") == 'ENG') {
            switch ($text) {
                case "range_check":
                    $trans_text = "Checking location.";
                    break;
                case "not_in_range":
                    $trans_text = "Not in the range where you can accept the queue.";
                    break;
                case "now_range":
                    $trans_text = "Current distance from check-in point";
                    break;
                case "name":
                    $trans_text = "Name";
                    break;
                case "dob":
                    $trans_text = "Date of Birth";
                    break;
                case "number":
                    $trans_text = "Number";
                    break;
                case "check":
                    $trans_text = "Check";
                    break;
                case "not_found":
                    $trans_text = "Not found!";
                    break;
                case "checkin_walkin":
                    $trans_text = "Click to receive the queue, No appointment.";
                    break;
                case "app_no":
                    $trans_text = "Appointment Number";
                    break;
                case "app_date":
                    $trans_text = "Appointment Date";
                    break;
                case "app_time":
                    $trans_text = "Appointment Time";
                    break;
                case "no_app":
                    $trans_text = "No Appointment";
                    break;
                case "check_app":
                    $trans_text = "<span style=\"font-size: 0.5rem\">Check Appointment</span>";
                    break;
                case "get_queue":
                    $trans_text = "Receive queue";
                    break;
                case "already_queue":
                    $trans_text = "Already received the queue";
                    break;
                case "cantCheckLocation":
                    $trans_text = "Check Location Error, Please refresh.";
                    break;
                default:
                    $trans_text = $text;
                    break;
            }
        } else {
            switch ($text) {
                case "range_check":
                    $trans_text = "กำลังเช็คสถานที่";
                    break;
                case "not_in_range":
                    $trans_text = "ไม่อยู่ในระยะที่สามารถกดรับคิวได้";
                    break;
                case "now_range":
                    $trans_text = "ระยะปัจจุบันห่างจากจุดเช็คอิน";
                    break;
                case "name":
                    $trans_text = "ชื่อ";
                    break;
                case "dob":
                    $trans_text = "วันเกิด";
                    break;
                case "number":
                    $trans_text = "หมายเลข";
                    break;
                case "check":
                    $trans_text = "ตรวจสอบ";
                    break;
                case "not_found":
                    $trans_text = "ไม่พบข้อมูล";
                    break;
                case "checkin_walkin":
                    $trans_text = "กดเพื่อรับคิวไม่ได้นัด";
                    break;
                case "app_no":
                    $trans_text = "หมายเลขนัด";
                    break;
                case "app_date":
                    $trans_text = "วันที่นัด";
                    break;
                case "app_time":
                    $trans_text = "เวลานัด";
                    break;
                case "no_app":
                    $trans_text = "ไม่มีคิวนัดวันนี้";
                    break;
                case "check_app":
                    $trans_text = "เช็คนัด";
                    break;
                case "get_queue":
                    $trans_text = "รับคิว";
                    break;
                case "already_queue":
                    $trans_text = "รับคิวไปแล้ว";
                    break;
                case "cantCheckLocation":
                    $trans_text = "ไม่สามารถเช็คตำแหน่งได้ โปรดลองอีกครั้ง";
                    break;
                default:
                    $trans_text = $text;
                    break;
            }
        }

        return $trans_text;
    }
    private function formatName($first, $last)
    {
        mb_internal_encoding('UTF-8');
        $setname = mb_substr($first, 1);
        $setlast = mb_substr($last, 1);
        if (str_contains($setname, '\\')) {
            $setname = explode("\\", $setname);
            $setname = $setname[1] . $setname[0];
        }
        $fullname = $setname . " " . $setlast;

        return $fullname;
    }
    public function index()
    {
        $hashHN = '3d571e75ce9d28ddb272fde4898a667d895cec492fecb8526b7b957f7b7c3775';

        $text = (object) [
            'name' => $this->lang('name'),
            'app_no' => $this->lang('app_no'),
            'app_date' => $this->lang('app_date'),
            'app_time' => $this->lang('app_time'),
            'range_check' => $this->lang('range_check'),
        ];

        $getHN = DB::connection('SMS')
            ->table('TB_HAS_HN')
            ->where('hasHN', $hashHN)
            ->first();

        if ($getHN == null) {
            $hn = $hashHN;
        } else {
            $hn = $getHN->HN;
        }

        $data = Master::whereDate('check_in', date('Y-m-d'))->where('hn', $hn)->first();
        if ($data !== null) {
            return view('getQueue')->with(compact('data'));
        }

        $hnDetail = DB::connection('SSB')
            ->table('HNPAT_INFO')
            ->leftjoin('HNPAT_NAME', 'HNPAT_INFO.HN', '=', 'HNPAT_NAME.HN')
            ->leftjoin('HNPAT_REF', 'HNPAT_INFO.HN', '=', 'HNPAT_REF.HN')
            ->leftjoin('HNPAT_ADDRESS', 'HNPAT_INFO.HN', '=', 'HNPAT_ADDRESS.HN')
            ->whereNull('HNPAT_INFO.FileDeletedDate')
            ->where('HNPAT_INFO.HN', $hn)
            ->where('HNPAT_ADDRESS.SuffixTiny', 1)
            ->where('HNPAT_NAME.SuffixSmall', 0)
            ->select(
                'HNPAT_INFO.HN',
                'HNPAT_INFO.BirthDateTime',
                'HNPAT_INFO.NationalityCode',
                'HNPAT_NAME.FirstName',
                'HNPAT_NAME.LastName',
                'HNPAT_REF.RefNo',
                'HNPAT_ADDRESS.MobilePhone'
            )
            ->first();
        if ($hnDetail == null) {
            $hnDetail = (object) [
                'name' => 'No Data',
                'HN' => $hn,
                'appNo' => 'No Data',
                'appDate' => 'No Data',
                'appTime' => 'No Data',
            ];

            return view('index')->with(compact('hnDetail', 'text'));
        }

        $hnDetail->name = $this->formatName($hnDetail->FirstName, $hnDetail->LastName);
        ($hnDetail->NationalityCode == 'THA') ? session()->put('langSelect', "TH") : session()->put('langSelect', "ENG");

        $myApp = DB::connection('SSB')
            ->table('HNAPPMNT_HEADER')
            ->whereDate('HNAPPMNT_HEADER.AppointDateTime', date('Y-m-d'))
            ->where('HNAPPMNT_HEADER.Clinic', '1800')
            ->where('HNAPPMNT_HEADER.HN', $hn)
            ->orderBy('HNAPPMNT_HEADER.AppointDateTime', 'ASC')
            ->first();

        if ($myApp !== null) {
            $strTime = strtotime($myApp->AppointDateTime);
            $hnDetail->appNo = $myApp->AppointmentNo;
            $hnDetail->appDate = date('d M Y', $strTime);
            $hnDetail->appTime = date('H', $strTime) . ':00';
        } else {
            $hnDetail->appNo = $this->lang('no_app');
            $hnDetail->appDate = date('d M Y');
            $hnDetail->appTime = '-';
        }

        return view('index')->with(compact('hnDetail', 'text'));
    }
    public function changeLang(Request $request)
    {
        $lang = $request->lang;
        if ($lang == 'TH') {
            session()->put('langSelect', "TH");
        } elseif ($lang == "ENG") {
            session()->put('langSelect', "ENG");
        }

        return response()->json('success', 200);
    }
    public function latlogCheck($input_lat, $input_lon)
    {
        $base_lat = "13.7530601";
        $base_lon = "100.5688306";
        $theta = $input_lon - $base_lon;
        $dist = sin(deg2rad($input_lat)) * sin(deg2rad($base_lat)) + cos(deg2rad($input_lat)) * cos(deg2rad($base_lat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $outputDistant = $miles * 1.609344;

        return $outputDistant;
    }
    public function checkLocation(Request $request)
    {
        $hn = $request->hn;
        if ($request->lat == '-' || $request->log == '-') {
            $html = '<div class="text-center rounded p-3 btn-danger">' . $this->lang('cantCheckLocation') . '</div>';

            return response()->json(['status' => 'success', 'html' => $html], 200);
        }

        $myApp = DB::connection('SSB')
            ->table('HNAPPMNT_HEADER')
            ->whereDate('HNAPPMNT_HEADER.AppointDateTime', date('Y-m-d'))
            ->where('HNAPPMNT_HEADER.Clinic', '1800')
            ->where('HNAPPMNT_HEADER.HN', $hn)
            ->orderBy('HNAPPMNT_HEADER.AppointDateTime', 'ASC')
            ->first();

        if ($myApp !== null) {
            $outputDistant = $this->latlogCheck($request->lat, $request->log);

            if ($outputDistant > $this->LocationDistant) {
                $html = '<div class="text-center rounded p-3 btn-danger">';
                $html .= '<div>' . $this->lang('not_in_range') . '</div>';
                $html .= '<div>' . $this->lang('now_range') . ' : ' . round($outputDistant, 1) . ' Km</div>';
                $html .= '</div>';
            } else {
                $findAleadry = Master::whereDate('check_in', date('Y-m-d'))->where('hn', $hn)->whereNull('success_by')->first();
                ($findAleadry !== null)
                ? $html = '<div class="text-center rounded p-3 btn-danger">' . $this->lang('already_queue') . '</div>'
                : $html = '<div id="sleItem" onclick="selectItem(\'' . $hn . '\')" class="text-center rounded p-3 btn-sms">' . $this->lang('get_queue') . '</div>';
            }
        } else {
            $html = '<div class="text-center rounded p-3 btn-danger">' . $this->lang('no_app') . '</div>';
        }

        return response()->json(['status' => 'success', 'html' => $html], 200);
    }
}
