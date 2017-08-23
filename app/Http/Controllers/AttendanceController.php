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

//$d1 = Carbon::parse('2017/8/23 19:30:00');
//$d2 = Carbon::parse('2017/8/23 20:25:00');
//dd(intval(round($d1->diffInMinutes($d2)/30))*0.5);
//dd(Carbon::parse('2017/8/23 23:35:45')->subDay()->toDateString());
//dd(Carbon::parse('2017/8/20 23:35:45')->dayOfWeek);
//dd(Carbon::parse('2017/8/20 23:35:45')->format('i:s'));

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

            $cellData[] = ['部门', '姓名', '迟到', '早退', '病假', '事假', '旷工', '婚假', '产假/陪产假', '年休假', '丧假', '调休', '公假', '出勤', '员工签字', '备注'];

            foreach ($employees as $attendanceID => &$employee) {
                // 个人考勤汇总
                $singlePersonRecords = $attendanceRecords[$attendanceID];
                ksort($singlePersonRecords);

                $allDayRecords = [];
                $attendanceSummary = [];
                $attendanceSummary['late_in_morning'] = [];

                foreach ($singlePersonRecords as $thatDay => $thatDayObj) {
                    $thatDayTime = $thatDayObj->format('H:i');
                    $thatDayDateTime = $thatDayObj->toDateTimeString();
                    // echo($thatDayTime . '|' . $thatDay . '<br>');
                    if ($thatDayTime >= '06:00' && $thatDayTime <= '10:30') {
                        $allDayRecords[$thatDayObj->toDateString()]['morning'][] = $thatDayDateTime;
                    } elseif ($thatDayTime >= '17:30' && $thatDayTime <= '23:59') {
                        $allDayRecords[$thatDayObj->toDateString()]['evening'][] = $thatDayDateTime;
                    } elseif ($thatDayTime >= '00:00' && $thatDayTime < '06:00') {
                        $allDayRecords[$thatDayObj->subDay()->toDateString()]['evening'][] = $thatDayDateTime;
                    }
                }

                $freeLateTimes = 3;
                // 加班时间总计
                $attendanceSummary['overtime_total'] = 0;
                // 晚上8点后加班次数
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
                        $dayBeforeEveningSignTime = '17:30';
                        if (!empty($allDayRecords[$dayBefore]['evening'])) {
                            // 进入前一天加班迟到判断
                            $dayBeforeEveningSignTime = Carbon::parse(max($allDayRecords[$dayBefore]['evening']))->format('H:i');
                        }
                        // 晚上21点之前下班不能迟到
                        if ($dayBeforeEveningSignTime >= '17:30' && $dayBeforeEveningSignTime < '21:00') {
                            if ($earliestSignTime > '09:00') {
                                if ($earliestSignTime <= '09:10') {
                                    if ($freeLateTimes > 0) {
                                        // 免费机会如果没用完则抵消一次
                                        $freeLateTimes--;
                                    } else {
                                        // 免费机会用完则算迟到
                                        $attendanceSummary['late_in_morning'][] = $earliestSignDateTime;
                                    }
                                } else {
                                    // 超过10分钟直接算迟到
                                    $attendanceSummary['late_in_morning'][] = $earliestSignDateTime;
                                }
                            }
                        } elseif ($dayBeforeEveningSignTime >= '21:00' && $dayBeforeEveningSignTime < '22:00') {
                            // 加班到晚上22点之前，次日9点半之前不算迟到
                            if ($earliestSignTime > '09:30') {
                                $attendanceSummary['late_in_morning'][] = $earliestSignDateTime;
                            }
                        } elseif ($dayBeforeEveningSignTime >= '22:00' && $dayBeforeEveningSignTime <= '23:59') {
                            // 加班到晚上0点之前，次日10点之前不算迟到
                            if ($earliestSignTime > '10:00') {
                                $attendanceSummary['late_in_morning'][] = $earliestSignDateTime;
                            }
                        }
                    }
                    // 加班统计overtime
                    if (!empty($thatDayRecords['evening'])) {
                        $latestSignDateTime = max($thatDayRecords['evening']);
                        $latestSignObj = Carbon::parse($latestSignDateTime);
                        $latestSignTime = $latestSignObj->format('H:i');
                        // 晚上加班19:30开始统计，起步1小时，之后以半小时为单位统计
                        if ($latestSignTime >= '19:30' || $latestSignTime < '06:00') {
                            // 保底1小时
                            // 计算增量时长
                            $overtimeBaseObj = Carbon::parse($thatDay . ' 19:30:00');
                            $deltaHours = round($latestSignObj->diffInMinutes($overtimeBaseObj)/30)*0.5;
                            $attendanceSummary['overtime_total'] += 1 + $deltaHours;
                        }
                        if ($latestSignTime >= '20:00' || $latestSignTime < '06:00') {
                            $attendanceSummary['overtime_after_eight']++;
                        }
                    }
                }
                // dd($allDayRecords);
                // dd($attendanceSummary);

                $notes = [];
                if (!empty($attendanceSummary['overtime_after_eight'])) {
                    $notes[] = '加班8点后: ' . $attendanceSummary['overtime_after_eight'] . '次';
                }
                if (!empty($attendanceSummary['overtime_total'])) {
                    $notes[] = '加班总时长: ' . $attendanceSummary['overtime_total'] . '小时';
                }
                if (!empty($attendanceSummary['late_in_morning'])) {
                    $notes[] = '迟到' . count($attendanceSummary['late_in_morning']) . '次: ' . implode(',', $attendanceSummary['late_in_morning']);
                }
                if (!empty($attendanceSummary['no_record_all_day'])) {
                    $notes[] = '全天无打卡' . count($attendanceSummary['no_record_all_day']) . '次: ' . implode(',', $attendanceSummary['no_record_all_day']);
                }
                if (!empty($attendanceSummary['no_record_all_morning'])) {
                    $notes[] = '早上无打卡' . count($attendanceSummary['no_record_all_morning']) . '次: ' . implode(',', $attendanceSummary['no_record_all_morning']);
                }
                if (!empty($attendanceSummary['no_record_all_evening'])) {
                    $notes[] = '晚上无打卡' . count($attendanceSummary['no_record_all_evening']) . '次: ' . implode(',', $attendanceSummary['no_record_all_evening']);
                }
                // dd($employee);

                $employee['attendanceNotes'] = implode('; ', $notes);
                $employee['attendanceSummary'] = $attendanceSummary;

                $cellData[] = [
                    $employee['department'], $employee['name'], count($attendanceSummary['late_in_morning']),
                    '', '', '', '', '', '', '', '', '', '', count($allDayRecords),
                    '', $employee['attendanceNotes']];

                // if ($attendanceID == 149)
                //     dd($employee);
            }
           Excel::create('考勤统计',function($excel) use ($cellData) {
               $excel->sheet('考勤统计', function($sheet) use ($cellData) {
                   $sheet->rows($cellData);
               });
           })->export('xls');
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
