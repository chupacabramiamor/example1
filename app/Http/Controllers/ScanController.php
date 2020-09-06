<?php

namespace App\Http\Controllers;

use App\Services\ScanService;
use App\Services\WebsiteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\File;

class ScanController extends Controller
{
    private $scanSvc;

    public function __construct(ScanService $scanSvc, WebsiteService $websiteSvc)
    {
        $this->scanSvc = $scanSvc;
        $this->websiteSvc = $websiteSvc;
    }

    public function fast(Request $request)
    {
        $this->scanSvc->setOrigin($request->input('origin'));

        // Отправка запроса боту сканирования
        $scan = $this->scanSvc->run(10);

        // Запрос на формирование скриншота
        $screenshotUrl = $this->storeImageFile($this->scanSvc->captureScreenshot());

        return [
            'screenshot_url' => $screenshotUrl,
            'channel' => "scan.{$scan->id}",
            // 'host' =>
        ];
    }

    public function regular(Request $request)
    {
        # code...
    }

    private function storeImageFile(File $file) : string
    {
        $filename = sprintf('%s.png', str_random(8));

        $file->move(storage_path('app/public/websites'), $filename);

        return url("/websites/{$filename}");
    }
}
