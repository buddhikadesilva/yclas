<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-header">
                    <a href="<?= Route::url('calendar') . '?month=' . $previous_month->format('Y-m') ?>" class="btn btn-default pull-left">
                        <i class="fa fa-angle-left"></i> <?= Date::get_month_name($previous_month) ?>
                    </a>
                    <a href="<?= Route::url('calendar') . '?month=' . $next_month->format('Y-m') ?>" class="btn btn-default pull-right">
                        <?= Date::get_month_name($next_month) ?> <i class="fa fa-angle-right"></i>
                    </a>

                    <h1 class="text-center">
                        <?= Date::get_month_name($month) ?> <?= $month->format('Y') ?>
                    </h1>
                </div>

                <div class="calendar">
                    <div class="row">
                        <? $i = 0; ?>
                        <? foreach ($month_days as $key => $day) : ?>
                            <? if ($key === 0) : ?>
                                <? $blank = $day->format('w'); $i = $blank ?>
                                <? while ($blank > 0) : ?>
                                    <div class="col-xs-12 calendar-day calendar-no-current-month"></div>
                                    <? $blank = $blank - 1 ?>
                                <? endwhile ?>
                            <? endif ?>

                            <div class="col-xs-12 calendar-day">
                                <time datetime="<?= $day->format('Y-m-d') ?>">
                                    <?= $day->format('d') ?>
                                </time>

                                <div class="events">
                                    <? if (isset($month_ads[$day->format('Y-m-d')]['ads'])) : ?>
                                        <? foreach ($month_ads[$day->format('Y-m-d')]['ads'] as $ad) : ?>
                                            <div class="event">
                                                <h4>
                                                    <a href="<?=Route::url('ad', ['controller' => 'ad', 'category' => $ad->category->seoname, 'seotitle' => $ad->seotitle])?>">
                                                        <?= $ad->title ?>
                                                    </a>
                                                </h4>
                                            </div>
                                        <? endforeach ?>
                                    <? endif ?>
                                </div>
                            </div>

                            <? $i++; ?>
                            <? if (($i % 7) === 0 ) : ?>
                                </div><div class="row">
                            <? endif ?>
                        <? endforeach ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
