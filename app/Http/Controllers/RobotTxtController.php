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
        if($_SESSION['allow'] == 1){
            $this->robotTxtRepository->findDirectiveSiteMap();
            $result = $this->robotTxtRepository->findDirectiveHost();
//            $this->robotTxtRepository->saveToExcel();
        }


    }

    public function check()
    {
//        $this->robotTxtRepository->check();

        return redirect()->back();

    }

    public function saveToExcel()
    {
        $this->robotTxtRepository->saveToExcel();
    }
}
