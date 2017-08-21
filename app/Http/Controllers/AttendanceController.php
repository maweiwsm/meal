<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('frontend.orders.index', [
            'orders' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        dd('上传考勤');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        dd('保存考勤');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        dd('展示考勤');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function import(){
        $filePath = 'storage/app/public/attendance/2017/5月原始考勤数据.xlsx';


// dd(Carbon::parse('2017/8/13 23:35:45')->subDay()->toDateString());
// dd(Carbon::parse('2017/8/20 23:35:45')->dayOfWeek);
// dd(Carbon::parse('2017/8/20 23:35:45')->format('i:s'));

        Excel::load($filePath, function($reader) {
            // 考勤数据
            $employees = [];
            $attendanceRecords = [];
            $sheets = $reader->toArray();
            foreach ($sheets as $sheet) {
                foreach ($sheet as $row) {
                    // dd($row);
                    if (!empty($row['考勤号码'])) {
                        if (empty($employees[$row['考勤号码']])) {
                            $employees[$row['考勤号码']] = [
                                'department' => $row['部门'],
                                'name'       => $row['姓名']
                            ];
                        }
                        if (!empty($row['日期时间'])) {
                            $dateObj = Carbon::parse($row['日期时间']);
                            $attendanceRecords[$row['考勤号码']][$dateObj->toDateTimeString()] = $dateObj;
                        }
                    }
                }
            }
            foreach ($employees as $attendanceID => &$employee) {
                // 个人考勤汇总
                $singlePersonRecords = $attendanceRecords[$attendanceID];
                ksort($singlePersonRecords);

                $allDayRecords = [];
                $attendanceSummary = [];
                if ($attendanceID == 103) {
                    foreach ($singlePersonRecords as $thatDay => $thatDayObj) {
                        $time = $thatDayObj->format('H:i');
                        // echo($time . '|' . $thatDay . '<br>');
                        if ($time >= '06:00' && $time <= '10:30') {
                            $allDayRecords[$thatDayObj->toDateString()]['morning'][] = $thatDay;
                        } elseif ($time >= '17:30' && $time <= '23:59') {
                            $allDayRecords[$thatDayObj->toDateString()]['evening'][] = $thatDay;
                        } elseif ($time >= '00:00' && $time < '06:00') {
                            $allDayRecords[$thatDayObj->subDay()->toDateString()]['evening'][] = $thatDay;
                        }
                    }

                    $freeLateTimes = 3;
                    $attendanceSummary['overtime_after_eight'] = 0;

                    foreach ($allDayRecords as $thatDay => $thatDayRecords) {
                        // 先统计未打卡情况
                        if (empty($thatDayRecords['morning']) && empty($thatDayRecords['evening'])) {
                            // 全天未打卡
                            $attendanceSummary['no_record_all_day'][] = $thatDay;
                        } elseif (empty($thatDayRecords['morning']) && !empty($thatDayRecords['evening'])) {
                            // 早上未打卡
                            $attendanceSummary['no_record_all_morning'][] = $thatDay;
                        } elseif (!empty($thatDayRecords['morning']) && empty($thatDayRecords['evening'])) {
                            // 晚上未打卡
                            $attendanceSummary['no_record_all_evening'][] = $thatDay;
                        }
                        // 再统计迟到情况
                        if (!empty($thatDayRecords['morning'])) {
                            $earliestSignDateTime = min($thatDayRecords['morning']);
                            $earliestSignObj = Carbon::parse($earliestSignDateTime);
                            $earliestSignTime = $earliestSignObj->format('H:i');
                            $dayBefore = $earliestSignObj->subDay()->toDateString();
                            $dayBeforeEveningSignTime = '';
                            if (!empty($allDayRecords[$dayBefore]['evening'])) {
                                // 进入前一天加班迟到判断
                                $dayBeforeEveningSignTime = Carbon::parse(max($allDayRecords[$dayBefore]['evening']))->format('H:i');
                            }

                            if ($earliestSignTime > '09:00') {
                                $attendanceSummary['late_in_morning'][] = $thatDay;
                            }

                            if ($freeLateTimes > 0) {

                            }
                        }
                        // 加班统计overtime
                        if (!empty($thatDayRecords['evening'])) {
                            $latestSignDateTime = max($thatDayRecords['evening']);
                            $latestSignObj = Carbon::parse($latestSignDateTime);
                            $latestSignTime = $latestSignObj->format('H:i');
                            if ($latestSignTime >= '20:00' || $latestSignTime < '06:00') {
                                $attendanceSummary['overtime_after_eight']++;
                            }
                        }
                    }
//                    dd($allDayRecords);
                    dd($attendanceSummary);
                }

                $employee['attendanceSummary'] = $attendanceSummary;
            }
            // dd($attendanceRecords);
            dd($employees);
            // $reader->each(function($sheet) use (&$attendance) {
            //     $sheet->each(function($row) use (&$attendance) {
            //         //$attendance[]
            //         dd($row['日期时间']);
            //         dd($row['日期时间']->format('Y-m-d'));
            //         print_r($row['日期时间']->format('Y-m-d'));
            //         print '================================<br>';
            //     });
            //
            // });
        });
    }
}
