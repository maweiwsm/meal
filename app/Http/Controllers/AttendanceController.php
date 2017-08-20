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
            foreach ($employees as $attendanceID => $employee) {
                // 当月考勤
                $dateRecords = $attendanceRecords[$attendanceID];
                ksort($dateRecords);

                if ($attendanceID == 149) {
                    foreach ($dateRecords as $date => $dateObj) {
                        $time = $dateObj->format('H:i');
                        // echo($time . '|' . $date . '<br>');
                        if ($time >= '06:00' && $time <= '10:30') {
                            $employee[$dateObj->toDateString()]['mornings'][] = $date;
                        } elseif ($time >= '17:30' && $time <= '23:59') {
                            $employee[$dateObj->toDateString()]['evenings'][] = $date;
                        } elseif ($time >= '00:00' && $time < '06:00') {
                            $employee[$dateObj->subDay()->toDateString()]['evenings'][] = $date;
                        }
                    }
                    // dd($dateRecords);
                    dd($employee);
                }

                $employee['attendanceRecords'] = '';
            }
            dd($attendanceRecords);
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
