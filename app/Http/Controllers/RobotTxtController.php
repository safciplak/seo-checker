<?php

namespace App\Http\Controllers;

use App\Http\Repositories\RobotTxtRepository;

class RobotTxtController extends Controller
{
    /**
     * @var RobotTxtRepository
     */
    private $robotTxtRepository;

    /**
     * RobotTxtController constructor.
     * @param RobotTxtRepository $robotTxtRepository
     */
    public function __construct(RobotTxtRepository $robotTxtRepository)
    {

        $this->robotTxtRepository = $robotTxtRepository;
        $this->robotTxtRepository->check();

    }

    /**
     * Check
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check()
    {
        if (session('allow') == 1) {
            $this->robotTxtRepository->findDirectiveSiteMap();
            $result = $this->robotTxtRepository->findDirectiveHost();
            session(['array' => array_sort($result)]);
//            $this->robotTxtRepository->saveToExcel();
        }

        $array = session('array');
        $address = session('address');

//        echo '<pre>'; print_r($array); die;

        return view('welcome', compact('array', 'address'));
//        return redirect()->back();

    }

    /**
     * Save To Excel
     *
     */
    public function saveToExcel()
    {
        $this->robotTxtRepository->saveToExcel();
    }
}
