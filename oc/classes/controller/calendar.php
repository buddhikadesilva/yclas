<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Calendar extends Controller {

	public function action_index()
	{
        if (! (New Model_Field())->get('eventdate'))
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }

        $month = Core::request('month', date('Y-m'));

        // We assure is a proper time stamp if not we transform it
        if (is_string($month) === TRUE)
        {
            $month = strtotime($month);
        }

        $previous_month = (new DateTime())->setTimestamp($month)->modify('-1 month');
        $next_month = (new DateTime())->setTimestamp($month)->modify('+1 month');
        $month = (new DateTime())->setTimestamp($month);

        for($i = 1; $i <=  $month->format('t'); $i++)
        {
           $month_days[] = (new DateTime())->setTimestamp(
                strtotime($month->format('Y-m') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT))
            );
        }

        $this->template->title  = __('Calendar');

        Controller::$full_width = TRUE;

        $month_ads = DB::select(DB::expr('DATE(cf_eventdate) date'))
            ->select(DB::expr('COUNT(id_ad) count'))
            ->from('ads')
            ->where('status','=',Model_Ad::STATUS_PUBLISHED)
            ->where(DB::expr('cf_eventdate'), '>', Date::unix2mysql())
            ->where(DB::expr('MONTH(cf_eventdate)'), '=', DB::expr('MONTH("' . $month->format('Y-m-d') . '")'))
            ->where(DB::expr('YEAR(cf_eventdate)'), '=', DB::expr('YEAR("' . $month->format('Y-m-d') . '")'))
            ->group_by(DB::expr('DATE(cf_eventdate)'))
            ->order_by('date', 'asc')
            ->execute()
            ->as_array('date');

        foreach ($month_ads as $day) {
            $ads = (new Model_Ad())
                ->where('status','=',Model_Ad::STATUS_PUBLISHED)
                ->where(DB::expr('cf_eventdate'), '=', $day['date']);

            $month_ads[$day['date']]['ads'] = $ads->find_all();
        }

        $this->template->bind('content', $content);
        $this->template->content = View::factory('calendar', [
            'month_ads' => $month_ads,
            'month_days' => $month_days,
            'month' => $month,
            'previous_month' => $previous_month,
            'next_month' => $next_month,
        ]);
	}
}
