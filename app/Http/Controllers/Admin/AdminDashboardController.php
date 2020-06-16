<?php

namespace App\Http\Controllers\Admin;

use App\DashboardWidget;
use App\Helper\Reply;
use App\LeadFollowUp;
use App\Leave;
use App\ProjectActivity;
use App\Task;
use App\TaskboardColumn;
use App\Ticket;
use App\Traits\CurrencyExchange;
use App\UserActivity;
use Carbon\Carbon;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends AdminBaseController
{
    use CurrencyExchange,AppBoot;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.dashboard');
        $this->pageIcon = 'icon-speedometer';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskBoardColumn = TaskboardColumn::all();

        $completedTaskColumn = $taskBoardColumn->filter(function ($value, $key) {
            return $value->slug == 'completed';
        })->first();

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "client") as totalClients'),
                DB::raw('(select count(users.id) from `users` inner join role_user on role_user.user_id=users.id inner join roles on roles.id=role_user.role_id WHERE roles.name = "employee" and users.status = "active") as totalEmployees'),
                DB::raw('(select count(projects.id) from `projects`) as totalProjects'),
                DB::raw('(select count(invoices.id) from `invoices` where status = "unpaid") as totalUnpaidInvoices'),
                DB::raw('(select sum(project_time_logs.total_minutes) from `project_time_logs`) as totalHoursLogged'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id=' . $completedTaskColumn->id . ') as totalCompletedTasks'),
                DB::raw('(select count(tasks.id) from `tasks` where tasks.board_column_id != ' . $completedTaskColumn->id . ') as totalPendingTasks'),
                DB::raw('(select count(attendances.id) from `attendances` inner join users as atd_user on atd_user.id=attendances.user_id where DATE(attendances.clock_in_time) = CURDATE() and atd_user.status = "active") as totalTodayAttendance'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") and deleted_at IS NULL) as totalUnResolvedTickets'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="resolved" or status="closed") and deleted_at IS NULL) as totalResolvedTickets')
            )
            ->first();

        $timeLog = intdiv($this->counts->totalHoursLogged, 60) . ' ' . __('app.hrs') . ' ';

        if (($this->counts->totalHoursLogged % 60) > 0) {
            $timeLog .= ($this->counts->totalHoursLogged % 60) . ' '. __('app.mins');
        }

        $this->counts->totalHoursLogged = $timeLog;

        $this->pendingTasks = Task::with('project')
            ->where('tasks.board_column_id', '<>', $completedTaskColumn->id)
            ->where(DB::raw('DATE(due_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->orderBy('due_date', 'desc')
            ->select('tasks.*')
            ->limit(15)
            ->get();

        $this->pendingLeadFollowUps = LeadFollowUp::with('lead')->where(DB::raw('DATE(next_follow_up_date)'), '<=', Carbon::now()->timezone($this->global->timezone)->format('Y-m-d'))
            ->join('leads', 'leads.id', 'lead_follow_up.lead_id')
            ->where('leads.next_follow_up', 'yes')
            ->get();

        $this->newTickets = Ticket::where('status', 'open')
            ->orderBy('id', 'desc')->get();

        $this->projectActivities = ProjectActivity::with('project')
            ->join('projects', 'projects.id', '=', 'project_activity.project_id')
            ->whereNull('projects.deleted_at')->select('project_activity.*')
            ->limit(15)->orderBy('id', 'desc')->get();

        $this->userActivities = UserActivity::with('user')->limit(15)->orderBy('id', 'desc')->get();

        // earning chart
        $this->fromDate = Carbon::now()->timezone($this->global->timezone)->subDays(30);
        $this->toDate = Carbon::now()->timezone($this->global->timezone);
        $invoices = DB::table('payments')
            ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->where('paid_on', '>=', $this->fromDate)
            ->where('paid_on', '<=', $this->toDate)
            ->where('payments.status', 'complete')
            ->groupBy('date')
            ->orderBy('paid_on', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(paid_on,"%Y-%m-%d") as date'),
                DB::raw('sum(amount) as total'),
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            ]);

        $chartData = array();
        foreach ($invoices as $chart) {
            if ($chart->currency_code != $this->global->currency->currency_code) {
                if ($chart->is_cryptocurrency == 'yes') {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $usdTotal = ($chart->total * $chart->usd_price);
                            $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                        }
                    } else {
                        $usdTotal = ($chart->total * $chart->usd_price);
                        $chartData[] = ['date' => $chart->date, 'total' => floor($usdTotal / $chart->exchange_rate)];
                    }
                } else {
                    if ($chart->exchange_rate == 0) {
                        if ($this->updateExchangeRates()) {
                            $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                        }
                    } else {
                        $chartData[] = ['date' => $chart->date, 'total' => floor($chart->total / $chart->exchange_rate)];
                    }
                }
            } else {
                $chartData[] = ['date' => $chart->date, 'total' => round($chart->total, 2)];
            }
        }

        $this->chartData = json_encode($chartData);
        $this->leaves = Leave::with('user', 'type')
            ->where('status', '<>', 'rejected')
            ->whereYear('leave_date', Carbon::now()->timezone($this->global->timezone)->format('Y'))
            ->get();

        $this->progressPercent = $this->progressbarPercent();
        $this->widgets = DashboardWidget::all();
        $this->activeWidgets = DashboardWidget::where('status', 1)->get()->pluck('widget_name')->toArray();

        $this->isCheckScript();
        return view('admin.dashboard.index', $this->data);
    }

    private function progressbarPercent()
    {
        $totalItems = 4;
        $completedItem = 1;
        $progress = [];
        $progress['progress_completed'] = false;

        if ($this->global->company_email != 'company@email.com') {
            $completedItem++;
            $progress['company_setting_completed'] = true;
        }

        if ($this->smtpSetting->verified !== 0 || $this->smtpSetting->mail_driver == 'mail') {
            $progress['smtp_setting_completed'] = true;

            $completedItem++;
        }

        if ($this->user->email != 'admin@example.com') {
            $progress['profile_setting_completed'] = true;

            $completedItem++;
        }


        if ($totalItems == $completedItem) {
            $progress['progress_completed'] = true;
        }

        $this->progress = $progress;


        return ($completedItem / $totalItems) * 100;
    }

    public function widget(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        DashboardWidget::where('status', 1)->update(['status' => 0]);

        foreach ($data as $key => $widget) {
            DashboardWidget::where('widget_name', $key)->update(['status' => 1]);
        }

        return Reply::redirect(route('admin.dashboard'), __('messages.updatedSuccessfully'));
    }
}
