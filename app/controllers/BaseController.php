<?php

class BaseController extends Controller
{
    /** @var AmaotoUser */
    protected $CurrentUser;

    public function __construct()
    {
        // 判断浏览器名称和版本

        $agent = Request::header('user-agent');
        $browser = '';
        $browser_ver = '';

        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'Internet Explorer';
            $browser_ver = $regs[1];
        } elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'FireFox';
            $browser_ver = $regs[1];
        } elseif (preg_match('/Maxthon/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
            $browser_ver = '';
        } elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser = 'Opera';
            $browser_ver = $regs[1];
        } elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'OmniWeb';
            $browser_ver = $regs[2];
        } elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Netscape';
            $browser_ver = $regs[2];
        } elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Safari';
            $browser_ver = $regs[1];
        } elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
            $browser_ver = $regs[1];
        } elseif (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Lynx';
            $browser_ver = $regs[1];
        }

        if ($browser == 'Internet Explorer' && $browser_ver <= 8) {
            if (!Request::ajax()) {
                throw new BrowserNotSupportedException('浏览器不兼容');
            }
        }

        if (!Config::get('constants.installed') && !Request::is('install/*') && !Request::is('api/install')) {
            if (!Request::ajax()) {
                throw new AppNeedInstallException('应用未初始化');
            }
        }

        if (Auth::check()) {
            $this->CurrentUser = Auth::user();
            $this->CurrentUser->updateAct();
        } else {
            $this->CurrentUser = new AmaotoUser;
        }

        View::share('CurrentUser', $this->CurrentUser);
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

}